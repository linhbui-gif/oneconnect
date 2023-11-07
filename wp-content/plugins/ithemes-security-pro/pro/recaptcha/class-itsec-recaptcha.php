<?php

use iThemesSecurity\Contracts\Runnable;
use iThemesSecurity\Lib\Lockout\Host_Context;
use iThemesSecurity\Recaptcha\Provider;

class ITSEC_Recaptcha {
	const A_LOGIN = 'login';
	const A_REGISTER = 'register';
	const A_COMMENT = 'comment';
	const A_RESET_PASS = 'reset_pass';

	const HANDLE = 'itsec-recaptcha-script';
	const SDK_HANDLE = 'itsec-recaptcha-api';
	const OPT_IN_HANDLE = 'itsec-recaptcha-opt-in';

	/** @var array */
	private $settings;

	/** @var string */
	private $cookie_name;

	/** @var Provider\Provider */
	private $provider;

	/** @var true|WP_Error */
	private $cached_result;

	// Keep track of the number of recaptcha instances on the page
	private static $captcha_count = 0;

	public function run() {
		$this->cookie_name = 'itsec-recaptcha-opt-in-' . COOKIEHASH;
		$this->settings    = ITSEC_Modules::get_settings( 'recaptcha' );

		// Run on init so that we can use is_user_logged_in()
		// Warning: BuddyPress has issues with using is_user_logged_in() on plugins_loaded
		add_action( 'init', [ $this, 'setup' ], - 100 );

		add_filter( 'itsec_lockout_modules', [ $this, 'register_lockout_module' ] );

		// Check for the opt-in and set the cookie.
		if ( isset( $_REQUEST['recaptcha-opt-in'] ) && 'true' === $_REQUEST['recaptcha-opt-in'] ) {
			ITSEC_Lib::set_cookie( $this->cookie_name, 'true', [
				'length' => MONTH_IN_SECONDS,
			] );
		}
	}

	public function setup() {
		switch ( $this->settings['provider'] ) {
			case 'google':
				$this->provider = new Provider\Google( $this->settings );
				break;
			case 'cloudflare':
				$this->provider = new Provider\CloudFlare( $this->settings );
				break;
			case 'hcaptcha':
				$this->provider = new Provider\hCaptcha( $this->settings );
				break;
			default:
				return;
		}

		if ( ! $this->provider->is_configured() ) {
			return;
		}

		if ( $this->provider instanceof Runnable ) {
			$this->provider->run();
		}

		add_filter( 'itsec_get_privacy_policy_for_cookies', array( $this, 'get_privacy_policy_for_cookies' ) );
		add_filter( 'itsec_get_privacy_policy_for_sharing', array( $this, 'get_privacy_policy_for_sharing' ) );
		add_action( 'itsec_recaptcha_api_ready', [ $this, 'register_integrations' ] );

		ITSEC_Recaptcha_API::init( $this );

		// Logged-in users are people, we don't need to re-verify.
		if ( is_user_logged_in() ) {
			return;
		}

		add_action( 'login_head', [ $this, 'print_login_styles' ] );

		if ( $this->settings['comments'] ) {
			add_filter( 'comment_form_submit_button', [ $this, 'comment_form_submit_button' ] );
			add_filter( 'preprocess_comment', [ $this, 'validate_comment' ] );
		}

		if ( $this->settings['login'] ) {
			add_action( 'login_form', [ $this, 'login_form' ] );
			add_filter( 'login_form_middle', [ $this, 'wp_login_form' ], 100 );
			add_filter( 'authenticate', [ $this, 'validate_login' ], 30, 2 );
		}

		if ( $this->settings['register'] ) {
			add_action( 'register_form', [ $this, 'register_form' ] );
			add_filter( 'registration_errors', [ $this, 'validate_registration' ] );
		}

		if ( $this->settings['reset_pass'] ) {
			add_action( 'lostpassword_form', [ $this, 'reset_password_form' ] );
			add_action( 'lostpassword_post', [ $this, 'validate_reset_password' ] );
		}
	}

	public function register_integrations() {
		if ( function_exists( 'WC' ) ) {
			$woocommerce = new ITSEC_Recaptcha_Integration_WooCommerce();
			$woocommerce->run();
		}

		if ( class_exists( LifterLMS::class ) ) {
			$lifter = new ITSEC_Recaptcha_Integration_LifterLMS();
			$lifter->run();
		}

		if ( function_exists( 'restrict_content_pro' ) ) {
			$rcp = new ITSEC_Recaptcha_Integration_RCP();
			$rcp->run();
		}

	}

	public function print_login_styles() {
		echo '<style>#login { min-width: 350px !important; } .grecaptcha-badge { z-index: 1; }</style>';
	}

	/**
	 * Adds the CAPTCHA field to the login form.
	 *
	 * @since 1.13
	 */
	public function login_form(): void {
		$this->show_recaptcha( [ 'action' => self::A_LOGIN ] );
	}

	/**
	 * Adds the CAPTCHA field to the `wp_login_form()` template function.
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	public function wp_login_form( $html ) {
		$html .= $this->get_recaptcha( [ 'action' => self::A_LOGIN, 'margin' => [ 'top' => 10, 'bottom' => 10 ] ] );

		return $html;
	}

	/**
	 * Validates that the user logging in has provided a valid CAPTCHA.
	 *
	 * @since 1.13
	 *
	 * @param WP_User|WP_Error|null $user     WP_User if the user is authenticated, WP_Error or null otherwise.
	 * @param string                $username The username used to login.
	 *
	 * @return WP_User|WP_Error|null
	 */
	public function validate_login( $user, $username ) {
		if ( empty( $_POST ) || ITSEC_Core::is_api_request() ) {
			return $user;
		}

		ITSEC_Lib::load( 'login' );

		$args = [ 'action' => self::A_LOGIN ];

		if ( $user instanceof WP_User ) {
			$args['user'] = $user->ID;
		} elseif ( $found_user = ITSEC_Lib_Login::get_user( $username ) ) {
			$args['user'] = $found_user->ID;
		} else {
			$args['username'] = $username;
		}

		$result = $this->validate_captcha( $args );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $user;
	}

	/**
	 * Adds the CAPTCHA to the comment form submit button.
	 *
	 * @since 1.17
	 *
	 * @param string $submit_button The submit button in the comment form
	 *
	 * @return string The submit button with our recaptcha field prepended
	 */
	public function comment_form_submit_button( $submit_button ) {
		return $this->get_recaptcha( [ 'action' => self::A_COMMENT ] ) . $submit_button;
	}

	/**
	 * Validates that the user commenting has provided a valid CAPTCHA.
	 *
	 * @since 1.13
	 *
	 * @param array $comment_data Comment data.
	 *
	 * @return array Comment data.
	 */
	public function validate_comment( $comment_data ) {
		$result = $this->validate_captcha( [ 'action' => self::A_COMMENT ] );

		if ( is_wp_error( $result ) ) {
			wp_die( $result->get_error_message() );
		}

		return $comment_data;
	}

	/**
	 * Adds the CAPTCHA field to the registration form.
	 *
	 * @since 1.13
	 */
	public function register_form(): void {
		$this->show_recaptcha( [ 'action' => self::A_REGISTER ] );
	}

	/**
	 * Validates that the user registering has provided a valid CAPTCHA.
	 *
	 * @since 1.13
	 *
	 * @param WP_Error $errors A WP_Error object containing any errors encountered
	 *                         during registration.
	 *
	 * @return WP_Error The same WP_Error instance.
	 */
	public function validate_registration( $errors ) {
		$result = $this->validate_captcha( [ 'action' => self::A_REGISTER ] );

		if ( is_wp_error( $result ) ) {
			$errors->add( $result->get_error_code(), $result->get_error_message() );
		}

		return $errors;
	}

	/**
	 * Adds the CAPTCHA field to the reset password form.
	 */
	public function reset_password_form() {
		$this->show_recaptcha( [ 'action' => self::A_RESET_PASS ] );
	}

	/**
	 * Validates that the user resetting their password has provided a valid CAPTCHA.
	 *
	 * @param WP_Error $errors
	 */
	public function validate_reset_password( $errors ) {
		$result = $this->validate_captcha( [ 'action' => self::A_RESET_PASS ] );

		if ( is_wp_error( $result ) ) {
			$errors->add( $result->get_error_code(), $result->get_error_message() );
		}
	}

	// Leave this in as iThemes Exchange relies upon it.
	public function show_field( $echo = true, $deprecated1 = true, $margin_top = 0, $margin_right = 0, $margin_bottom = 0, $margin_left = 0, $deprecated2 = null ) {
		$args = compact( 'margin_top', 'margin_right', 'margin_bottom', 'margin_left' );

		if ( $echo ) {
			$this->show_recaptcha( $args );
		} else {
			return $this->get_recaptcha( $args );
		}
	}

	public function show_recaptcha( $args = [] ) {
		$args['margin'] = wp_parse_args( $args['margin'] ?? [], [
			'top'    => 10,
			'bottom' => 10,
		] );

		echo $this->get_recaptcha( $args );
	}

	public function get_recaptcha( $args = [] ) {
		self::$captcha_count ++;

		$defaults = [
			'margin'     => [
				'top'    => 0,
				'right'  => 0,
				'bottom' => 0,
				'left'   => 0,
			],
			'action'     => '',
			'controlled' => false,
			'instance'   => self::$captcha_count,
		];
		$args     = wp_parse_args( $args, $defaults );

		$args['margin'] = wp_parse_args( $args['margin'], $defaults['margin'] );

		if ( $html = $this->show_opt_in( $args ) ) {
			return $html;
		}

		$this->provider->register_assets();
		wp_enqueue_script( self::SDK_HANDLE, $this->provider->get_sdk_url( true ), [ self::HANDLE ], null );

		return $this->provider->get_html( $args );
	}

	/**
	 * Checks that a valid CAPTCHA code was provided.
	 *
	 * This function is used both internally in iThemes Security and externally in other projects.
	 *
	 * @since 1.13
	 *
	 * @param array $args
	 *
	 * @return bool|WP_Error Returns true or a WP_Error object on error.
	 */
	public function validate_captcha( $args = [] ) {
		if ( $this->cached_result ) {
			return $this->cached_result;
		}

		$validated = $this->provider->validate( $args );

		if ( $validated->is_success() ) {
			if ( ! $this->settings['validated'] ) {
				ITSEC_Modules::set_setting( 'recaptcha', 'validated', true );
			}

			if ( $this->settings['last_error'] ) {
				ITSEC_Modules::set_setting( 'recaptcha', 'last_error', '' );
			}

			$this->cached_result = true;

			return $this->cached_result;
		}

		if (
			! $this->settings['validated'] &&
			$validated->get_error()->get_error_code() === 'itsec.recaptcha.form-not-submitted'
		) {
			ITSEC_Modules::set_setting(
				'recaptcha',
				'last_error',
				esc_html__( 'The Site Key may be invalid or unrecognized. Verify that you input the Site Key and Private Key correctly.', 'it-l10n-ithemes-security-pro' )
			);

			$this->cached_result = true;

			return $this->cached_result;
		}

		if (
			$validated->get_error()->get_error_code() === 'itsec.recaptcha.http-error' ||
			$validated->get_error()->get_error_code() === 'itsec.recaptcha.invalid-response'
		) {
			// Todo: Log that there was a temporary issue with reCAPTCHA.
			$this->cached_result = true;

			return $this->cached_result;
		}

		foreach ( $validated->get_error()->get_error_codes() as $code ) {
			if ( empty( $validated->get_error()->get_error_data( $code )['report_to_admin'] ) ) {
				continue;
			}

			ITSEC_Modules::set_setting( 'recaptcha', 'last_error', $validated->get_error()->get_error_message( $code ) );
		}

		$this->log_failed_validation( $validated->get_error(), $args );
		$this->cached_result = new WP_Error(
			'itsec.recaptcha.incorrect',
			$validated->get_error()->get_error_message( 'itsec.recaptcha.incorrect' )
		);

		return $this->cached_result;
	}

	private function show_opt_in( $args ): string {
		if ( ! ITSEC_Modules::get_setting( 'recaptcha', 'gdpr' ) ) {
			return '';
		}

		if ( $this->has_visitor_opted_in() ) {
			return '';
		}

		$this->enqueue_opt_in();

		$url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		if ( false === strpos( $url, '?' ) ) {
			$url .= '?recaptcha-opt-in=true';
		} else {
			$url .= '&recaptcha-opt-in=true';
		}

		$p1 = $this->provider->get_opt_in_text();
		$p2 = sprintf(
			esc_html__( '%1$sI agree to these terms%2$s.', 'it-l10n-ithemes-security-pro' ),
			'<a href="' . esc_url( $url ) . '" class="itsec-recaptcha-opt-in__agree">',
			'</a>'
		);

		$html = '<div class="itsec-recaptcha-opt-in">';
		$html .= '<p>' . $p1 . '</p>';
		$html .= '<p>' . $p2 . '</p>';
		$html .= '<script type="text-template" class="itsec-recaptcha-opt-in__template">' . $this->provider->get_html( $args ) . '</script>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Enqueue assets for the opt-in dialog.
	 */
	private function enqueue_opt_in() {
		wp_enqueue_style( self::OPT_IN_HANDLE, plugins_url( 'css/itsec-recaptcha.css', __FILE__ ), [], ITSEC_Core::get_plugin_build() );

		if ( ! $this->settings['on_page_opt_in'] ) {
			return;
		}

		if ( wp_script_is( self::OPT_IN_HANDLE ) ) {
			return;
		}

		$localize = [
			'sdk'  => $this->provider->get_sdk_url( false ),
			'load' => $this->provider->get_js_load_function(),
		];

		$this->provider->register_assets();
		wp_enqueue_script( self::OPT_IN_HANDLE, plugins_url( 'js/optin.js', __FILE__ ), [ 'jquery', self::HANDLE ], ITSEC_Core::get_plugin_build() );
		wp_localize_script( self::OPT_IN_HANDLE, 'ITSECRecaptchaOptIn', $localize );
	}

	private function has_visitor_opted_in(): bool {
		if ( isset( $_REQUEST['recaptcha-opt-in'] ) && 'true' === $_REQUEST['recaptcha-opt-in'] ) {
			return true;
		}

		if ( isset( $_COOKIE[ $this->cookie_name ] ) && 'true' === $_COOKIE[ $this->cookie_name ] ) {
			return true;
		}

		return false;
	}

	/**
	 * Logs when a user does not provide a valid CAPTCHA.
	 *
	 * @param WP_Error $error
	 * @param array    $args
	 */
	private function log_failed_validation( $error, $args ) {
		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;

		/**
		 * Fires when a user fails the reCAPTCHA test.
		 *
		 * @param WP_Error $error
		 * @param array    $args
		 */
		do_action( 'itsec_failed_recaptcha', $error, $args );

		ITSEC_Log::add_notice( 'recaptcha', 'failed-validation', $error );

		$data    = $error->get_error_data();
		$context = new Host_Context( 'recaptcha' );

		if ( ! empty( $data['args']['user'] ) ) {
			$context->set_login_user_id( $data['args']['user'] );
		} elseif ( ! empty( $data['args']['username'] ) ) {
			$context->set_login_username( $data['args']['username'] );
		}

		$itsec_lockout->do_lockout( $context );

		if ( 'itsec.recaptcha.form-not-submitted' === $error->get_error_code() ) {
			ITSEC_Dashboard_Util::record_event( 'recaptcha-empty' );
		} else {
			ITSEC_Dashboard_Util::record_event( 'recaptcha-invalid' );
		}
	}

	public function get_privacy_policy_for_cookies( $policy ) {
		switch ( ITSEC_Modules::get_setting( 'recaptcha', 'provider' ) ) {
			case 'google':
				$text = esc_html__( 'Some forms on this site require the use of Google\'s reCAPTCHA service before they can be submitted.', 'it-l10n-ithemes-security-pro' );
				break;
			case 'cloudflare':
				$text = esc_html__( 'Some forms on this site require the use of CloudFlare\'s Turnstile service before they can be submitted.', 'it-l10n-ithemes-security-pro' );
				break;
			case 'hcaptcha':
				$text = esc_html__( 'Some forms on this site require the use of the hCAPTCHA service before they can be submitted.', 'it-l10n-ithemes-security-pro' );
				break;
			default:
				return $policy;
		}

		$text .= ' ' . esc_html__( 'If you consent to this service, a cookie is created that stores your consent. This cookie deletes itself after thirty days.', 'it-l10n-ithemes-security-pro' );

		$suggested_text = '<strong class="privacy-policy-tutorial">' . __( 'Suggested text:', 'it-l10n-ithemes-security-pro' ) . ' </strong>';

		$policy .= sprintf( "<p>%s %s</p>\n", $suggested_text, $text );

		return $policy;
	}

	public function get_privacy_policy_for_sharing( $policy ) {
		$suggested_text = '<strong class="privacy-policy-tutorial">' . __( 'Suggested text:', 'it-l10n-ithemes-security-pro' ) . ' </strong>';

		$policy .= sprintf( "<p>%s %s</p>\n", $suggested_text, $this->provider->get_opt_in_text() );

		return $policy;
	}

	/**
	 * Registers the CAPTCHA lockout module.
	 *
	 * @since 1.13
	 *
	 * @param array $lockout_modules The list of lockout modules.
	 *
	 * @return array The list of lockout modules.
	 */
	public function register_lockout_module( $lockout_modules ) {
		$lockout_modules['recaptcha'] = [
			'type'   => 'recaptcha',
			'reason' => __( 'too many failed captcha submissions.', 'it-l10n-ithemes-security-pro' ),
			'label'  => __( 'CAPTCHA', 'it-l10n-ithemes-security-pro' ),
			'host'   => $this->settings['error_threshold'],
			'period' => $this->settings['check_period'],
		];

		return $lockout_modules;
	}
}
