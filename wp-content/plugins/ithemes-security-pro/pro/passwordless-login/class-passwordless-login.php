<?php

use iThemesSecurity\PasswordlessLogin\Integrations\RestrictContentPro;
use iThemesSecurity\PasswordlessLogin\Integrations\WooCommerce;
use iThemesSecurity\PasswordlessLogin\Integrations\LifterLMS;
use iThemesSecurity\PasswordlessLogin\Integrations\EasyDigitalDownloads;
use iThemesSecurity\Lib\Lockout\Host_Context;
use ITSEC_Passwordless_Login_Interstitial as Interstitial;
use ITSEC_Passwordless_Login_2fa_Setting_Interstitial as Setting_Interstitial;

require_once( dirname( __FILE__ ) . '/class-passwordless-login-interstitial.php' );
require_once( dirname( __FILE__ ) . '/class-passwordless-login-2Fa-setting-interstitial.php' );
require_once( dirname( __FILE__ ) . '/class-passwordless-login-utilities.php' );
require_once( dirname( __FILE__ ) . '/class-passwordless-login-integrations.php' );

class ITSEC_Passwordless_Login {

	const AJAX_ACTION_LOGIN_METHODS = 'itsec-get-login-methods';
	const MODAL_LOGIN = 'itsec-pwls-modal';

	const ACTION = 'itsec-passwordless-login-prompt';
	const HIDE = 'itsec-pwls-hide';
	const METHOD = 'itsec-pwls-method';
	const NOTIFICATION = 'passwordless-login';

	const E_NOT_ALLOWED = 'itsec-passwordless-login-not-allowed';

	const FLOW_USER_FIRST = 'username-first';
	const FLOW_METHOD_FIRST = 'method-first';

	/** @var WP_Error */
	private $error;

	/** @var string */
	private $flow_type;

	/**
	 * ITSEC_Magic_Links constructor.
	 */
	public function __construct() {
		$this->error     = new WP_Error();
		$this->flow_type = ITSEC_Modules::get_setting( 'passwordless-login', 'flow' );
	}

	public function run() {
		ITSEC_Lib::load( 'login' );

		$this->register_integrations();
		ITSEC_Passwordless_Login_Integrations::run();

		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ), 20 );
		add_action( 'login_enqueue_scripts', array( $this, 'register_assets' ), 20 );

		add_action( 'itsec_login_interstitial_init', array( $this, 'register_interstitial' ) );
		add_filter( 'wp_login_errors', array( $this, 'modify_login_errors' ) );
		add_action( 'wp_authenticate', array( $this, 'strip_credentials_from_wp_signon' ), 10, 2 );
		add_action( 'wp_ajax_' . self::AJAX_ACTION_LOGIN_METHODS, array( $this, 'ajax_get_login_methods' ) );
		add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION_LOGIN_METHODS, array( $this, 'ajax_get_login_methods' ) );

		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_profile_scripts' ) );
		add_action( 'show_user_profile', array( $this, 'render_profile_fields' ), 9 );
		add_action( 'edit_user_profile', array( $this, 'render_profile_fields' ), 9 );

		add_action( 'init', array( $this, 'register_block' ) );

		add_filter( 'itsec_notifications', array( $this, 'register_notification' ) );
		add_filter( 'itsec_' . self::NOTIFICATION . '_notification_strings', array( $this, 'notification_strings' ) );

		if ( ! empty( $_GET[ self::E_NOT_ALLOWED ] ) ) {
			return;
		}

		add_action( 'login_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'login_body_class', array( $this, 'login_body_class' ), 10, 2 );
		add_action( 'login_form', array( $this, 'add_ui' ) );
		add_action( 'login_form_' . self::ACTION, array( $this, 'render_magic_link_action_page' ) );
		add_action( 'login_form_' . self::ACTION, array( $this, 'maybe_send_magic_link' ), 9 );
	}

	/**
	 * Register the assets in this module.
	 */
	public function register_assets() {
		wp_register_script( 'itsec-pwls-login-modal', plugin_dir_url( __FILE__ ) . 'js/modal.js', [ 'jquery' ] );
		wp_register_style( 'itsec-pwls-login-modal', plugin_dir_url( __FILE__ ) . 'css/modal.css' );

		if ( wp_script_is( 'itsec-passwordless-login-profile' ) ) {
			$this->register_profile_assets();
		} else {
			add_action( 'wp_footer', function () {
				if ( wp_script_is( 'itsec-passwordless-login-profile' ) ) {
					$this->register_profile_assets();
				}
			} );
		}
	}

	/**
	 * Registers Passwordless Login assets for the Profile screen and Manage Block.
	 *
	 * @return void
	 */
	private function register_profile_assets() {
		$user = wp_get_current_user();

		if ( ! ITSEC_Passwordless_Login_Utilities::is_available_for_user( $user ) ) {
			return;
		}

		$request = new WP_REST_Request( 'GET', '/wp/v2/users/me' );
		$request->set_query_params( [ 'context' => 'edit' ] );
		$response = rest_do_request( $request );

		if ( ! $response->is_error() ) {
			wp_add_inline_script( 'itsec-passwordless-login-profile', sprintf(
				"wp.data.dispatch('%s').receiveCurrentUserId( %d );", 'ithemes-security/core', $user->ID
			) );
			wp_add_inline_script( 'itsec-passwordless-login-profile', sprintf(
				"wp.data.dispatch('%s').receiveUser( %s );", 'ithemes-security/core', wp_json_encode( $response->get_data() )
			) );
		}

		do_action( 'itsec_passwordless_login_enqueue_profile_scripts', $user );
	}

	/**
	 * Register the login interstitial.
	 *
	 * @param ITSEC_Lib_Login_Interstitial $lib
	 */
	public function register_interstitial( ITSEC_Lib_Login_Interstitial $lib ) {
		$lib->register( Interstitial::SLUG, new Interstitial() );
		$lib->register( Setting_Interstitial::SLUG, new Setting_Interstitial() );
	}

	/**
	 * Enqueue the necessary JS and CSS.
	 */
	public function enqueue() {
		global $action;

		if ( $this->should_include_ui( $action ) || self::ACTION === $action || 'itsec-passwordless-login' === $action ) {
			wp_enqueue_style( 'itsec-passwordless-login', plugin_dir_url( __FILE__ ) . 'css/login.css', [], 3 );
		}

		if ( $this->should_include_ui( $action ) && empty( $_GET[ self::HIDE ] ) ) {
			$deps = array( 'jquery' );

			if ( in_array( 'webauthn', ITSEC_Passwordless_Login_Utilities::get_available_methods(), true ) ) {
				$deps[] = 'itsec-webauthn-utils';

				if ( ! is_user_logged_in() ) {
					$this->add_api_fetch_middleware();
				}
			}

			wp_enqueue_script( 'itsec-passwordless-login', plugin_dir_url( __FILE__ ) . 'js/login.js', $deps, 6 );
			wp_localize_script( 'itsec-passwordless-login', 'ITSECMagicLogin', array(
				'flow'           => $this->flow_type,
				'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
				'ajaxAction'     => self::AJAX_ACTION_LOGIN_METHODS,
				'magicAction'    => ITSEC_Lib::get_login_url( self::ACTION, '', 'login_post' ),
				'passwordAction' => ITSEC_Lib::get_login_url( '', '', 'login_post' ),
				'wpVersion'      => (float) ITSEC_Lib::get_wp_branch(),
				'i18n'           => array(
					'login' => esc_html__( 'Log In', 'it-l10n-ithemes-security-pro' ),
					'error' => esc_html__( 'Unknown error. Please try again later.', 'it-l10n-ithemes-security-pro' )
				),
			) );

			if ( self::FLOW_USER_FIRST === $this->flow_type ) {
				ITSEC_Lib::render( __DIR__ . '/templates/user-first/user-form-template.php', array(
					'user_lookup_fields_label' => ITSEC_Lib_Login::get_user_lookup_fields_label(),
				) );
			}
		}
	}

	/**
	 * Add classes to the login body depending on if magic link is active or not.
	 *
	 * @param array  $classes
	 * @param string $action
	 *
	 * @return array
	 */
	public function login_body_class( $classes, $action ) {
		if ( $this->should_include_ui( $action ) ) {
			$classes[] = 'no-js itsec-pwls-login itsec-pwls-login--flow-' . $this->flow_type;

			if ( self::FLOW_METHOD_FIRST === $this->flow_type ) {
				$classes[] = empty( $_GET[ self::HIDE ] ) ? 'itsec-pwls-login--show' : 'itsec-pwls-login--hide';
			} elseif ( self::FLOW_USER_FIRST === $this->flow_type ) {
				if ( ! $this->has_user() ) {
					$classes[] = 'itsec-pwls-login--no-user';
				} else {
					$classes[] = 'itsec-pwls-login--has-user';

					if (
						( $user = ITSEC_Lib_Login::get_user( $_POST['log'] ) ) &&
						ITSEC_Passwordless_Login_Utilities::get_enabled_methods_for_user( $user )
					) {
						$classes[] = 'itsec-pwls-login--is-available';
					}
				}
			}
		}

		return $classes;
	}

	/**
	 * Add Passwordless Login UI to the login form.
	 */
	public function add_ui() {
		if ( ! empty( $_GET[ self::HIDE ] ) && self::FLOW_METHOD_FIRST === $this->flow_type ) {
			ITSEC_Lib::render( __DIR__ . '/templates/fallback.php' );

			return;
		}

		if ( self::FLOW_METHOD_FIRST === $this->flow_type ) {
			echo '<input type="hidden" name="wp-submit" value="1">';

			ITSEC_Lib::render( __DIR__ . '/templates/method-first/login.php', array(
				'methods'                  => ITSEC_Passwordless_Login_Utilities::get_available_methods(),
				'user_lookup_fields_label' => ITSEC_Lib_Login::get_user_lookup_fields_label(),
				'prompt_link'              => ITSEC_Lib::get_login_url( self::ACTION ),
			) );
		}

		if ( self::FLOW_USER_FIRST === $this->flow_type ) {
			$user = empty( $_POST['log'] ) ? null : ITSEC_Lib_Login::get_user( $_POST['log'] );

			if ( ! $this->has_user() ) {
				echo '<input type="hidden" name="itsec_pwls_login_user_first" value="1">';
				add_filter( 'gettext', function ( $translation, $text, $domain ) {
					if ( 'default' === $domain && 'Log In' === $text ) {
						$translation = __( 'Continue', 'it-l10n-ithemes-security-pro' );
					}

					return $translation;
				}, 10, 3 );
			} else {
				$methods = $user ? ITSEC_Passwordless_Login_Utilities::get_enabled_methods_for_user( $user ) : [];
				ITSEC_Lib::render( __DIR__ . '/templates/user-first/login.php', array(
					'is_available' => count( $methods ) > 0,
					'methods'      => $methods,
					'prompt_link'  => add_query_arg( 'username', urlencode( $_POST['log'] ), ITSEC_Lib::get_login_url( self::ACTION ) ),
					'username'     => $_POST['log'],
				) );
			}
		}
	}

	/**
	 * Should the Passwordless login UI be included.
	 *
	 * We include on the login action, or any other action that falls through
	 * to the login action. ie they aren't rendering a custom page using `login_form_`.
	 *
	 * @param string $action
	 *
	 * @return bool
	 */
	private function should_include_ui( $action ) {
		if ( 'login' === $action || self::ACTION === $action ) {
			return true;
		}

		if ( doing_action( 'login_form_' . $action ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Whether we have the user being logged in as.
	 *
	 * If the login request has been processed, and errored, we no longer have a user and
	 * go back to the first step of the flow.
	 *
	 * @return bool
	 */
	private function has_user() {
		global $errors;

		if ( empty( $_POST['log'] ) ) {
			return false;
		}

		if ( is_wp_error( $errors ) && ( $code = $errors->get_error_code() ) && 'empty_password' !== $code ) {
			return false;
		}

		return true;
	}

	/**
	 * Render the magic link form submission page.
	 */
	public function render_magic_link_action_page() {
		$methods   = ITSEC_Passwordless_Login_Utilities::get_available_methods();
		$requested = $_GET[ self::METHOD ] ?? '';

		if ( in_array( $requested, $methods, true ) ) {
			$methods = [ $requested ];
		}

		ITSEC_Lib::render( __DIR__ . '/templates/prompt-page.php', array(
			'error'                    => $this->error,
			'username'                 => $_GET['username'] ?? '',
			'methods'                  => $methods,
			'use_recaptcha'            => ITSEC_Passwordless_Login_Utilities::use_recaptcha(),
			'user_lookup_fields_label' => ITSEC_Lib_Login::get_user_lookup_fields_label(),
		) );
		die;
	}

	/**
	 * Add login errors.
	 *
	 * @param WP_Error $error
	 *
	 * @return WP_Error
	 */
	public function modify_login_errors( $error ) {
		if ( ! empty( $_GET[ self::MODAL_LOGIN ] ) ) {
			$error->remove( 'expired' );
		}

		if ( ! empty( $_GET[ self::E_NOT_ALLOWED ] ) ) {
			$error->add(
				self::E_NOT_ALLOWED,
				__( 'Passwordless Login is not enabled for your account. Please login with your username and password.', 'it-l10n-ithemes-security-pro' )
			);
		}

		if ( self::FLOW_USER_FIRST === $this->flow_type ) {
			if ( ! empty( $_POST['itsec_pwls_login_user_first'] ) ) {
				$error->remove( 'empty_password' );
				$error->remove( 'empty_username' );
			} else {
				if (
					in_array( 'invalid_username', $error->get_error_codes(), true ) ||
					in_array( 'invalid_email', $error->get_error_codes(), true )
				) {
					unset( $_POST['log'] );
				}
			}
		}

		return $error;
	}

	/**
	 * Strip credentials from {@see wp_signon} when loading the non JS version of user first flow.
	 *
	 * This prevents other authentication methods from running.
	 *
	 * @param string $user_login
	 * @param string $user_password
	 */
	public function strip_credentials_from_wp_signon( &$user_login, &$user_password ) {
		if ( ! empty( $_POST['itsec_pwls_login_user_first'] ) && did_action( 'login_init' ) && self::FLOW_USER_FIRST === $this->flow_type ) {
			$user_login    = '';
			$user_password = '';
		}
	}

	/**
	 * Ajax endpoint to get the available login methods and HTML.
	 */
	public function ajax_get_login_methods() {
		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;

		if ( empty( $_POST['log'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'You must enter a username.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		$methods = [];

		if ( $user = ITSEC_Lib_Login::get_user( $_POST['log'] ) ) {
			$methods = ITSEC_Passwordless_Login_Utilities::get_enabled_methods_for_user( $user );
		}

		if ( ! $user ) {
			$context = new Host_Context( 'brute_force' );
			$context->set_login_username( $_POST['log'] );
			$itsec_lockout->do_lockout( $context );
		}

		wp_send_json_success( array(
			'methods' => $methods,
			'html'    => ITSEC_Lib::render( __DIR__ . '/templates/user-first/login-ajax.php', array(
				'username'           => $_POST['log'],
				'prompt_link'        => add_query_arg( 'username', urlencode( $_POST['log'] ), ITSEC_Lib::get_login_url( self::ACTION ) ),
				'is_available'       => in_array( 'magic', $methods, true ) || in_array( 'webauthn', $methods, true ),
				'methods'            => $methods,
				'user_lookup_fields' => ITSEC_Lib_Login::get_user_lookup_fields_label(),
			), false ),
		) );
	}

	/**
	 * Maybe send the magic link to the user.
	 */
	public function maybe_send_magic_link() {
		if ( ! isset( $_POST['itsec_magic_link_username'] ) ) {
			return;
		}

		if ( ! isset( $_POST['itsec_pwls_magic_login'] ) && ! isset( $_POST['itsec_pwls_webauthn_login'] ) ) {
			return;
		}

		$args = [
			'identifier' => $_POST['itsec_magic_link_username'],
			'method'     => isset( $_POST['itsec_pwls_webauthn_login'] ) ? 'webauthn' : 'magic',
		];

		if ( $args['method'] === 'webauthn' ) {
			$args['webauthn'] = $_POST['itsec_pwls_webauthn_login'];
		}

		$session_or_error = ITSEC_Passwordless_Login_Utilities::handle_login_request( $args );

		if ( is_wp_error( $session_or_error ) ) {
			if ( $session_or_error->get_error_message( 'itsec-passwordless-login-not-allowed' ) ) {
				wp_redirect( add_query_arg( self::E_NOT_ALLOWED, true, ITSEC_Lib::get_login_url() ) );
				die;
			}

			ITSEC_Lib::add_to_wp_error( $this->error, $session_or_error );

			return;
		}

		if ( $args['method'] === 'webauthn' ) {
			ITSEC_Core::get_login_interstitial()->do_next_step( $session_or_error );
		} else {
			ITSEC_Core::get_login_interstitial()->render_current_interstitial_or_login( $session_or_error );
		}
	}

	public function register_meta() {
		register_rest_field( 'user', 'itsec_passwordless_login', [
			'get_callback'    => function ( $user ) {
				if ( ! $user = get_userdata( $user['id'] ) ) {
					return null;
				}

				return [
					'available'         => ITSEC_Passwordless_Login_Utilities::is_available_for_user( $user ),
					'available_methods' => ITSEC_Passwordless_Login_Utilities::get_available_methods(),
					'2fa_used'          => ITSEC_Lib_User::is_user_using_two_factor( $user ),
					'2fa_enforced'      => ITSEC_Passwordless_Login_Utilities::is_2fa_enforced_for_user( $user ),
					'enabled'           => ITSEC_Passwordless_Login_Utilities::is_enabled_for_user( $user ),
					'2fa_enabled'       => ITSEC_Passwordless_Login_Utilities::is_2fa_enabled_for_user( $user ),
				];
			},
			'update_callback' => function ( $value, WP_User $user ) {
				if ( ! $value ) {
					return;
				}

				if ( isset( $value['enabled'] ) ) {
					ITSEC_Passwordless_Login_Utilities::set_enabled_for_user( $user, $value['enabled'] );
				}

				if ( isset( $value['2fa_enabled'] ) ) {
					ITSEC_Passwordless_Login_Utilities::set_2fa_enabled_for_user( $user, $value['2fa_enabled'] );
				}
			},
			'schema'          => [
				'context'    => [ 'edit' ],
				'type'       => 'object',
				'properties' => [
					'available'    => [
						'type'     => 'boolean',
						'readonly' => true,
					],
					'2fa_used'     => [
						'type'     => 'boolean',
						'readonly' => true,
					],
					'2fa_enforced' => [
						'type'     => 'boolean',
						'readonly' => true,
					],
					'enabled'      => [
						'type' => 'boolean',
					],
					'2fa_enabled'  => [
						'type' => 'boolean',
					],
				],
			]
		] );
	}

	/**
	 * Enqueue scripts for the Profile page.
	 */
	public function enqueue_profile_scripts() {
		global $pagenow, $user_id;

		if ( $pagenow !== 'profile.php' && $pagenow !== 'user-edit.php' ) {
			return;
		}

		$user = get_userdata( $user_id );

		if ( ! ITSEC_Passwordless_Login_Utilities::is_available_for_user( $user ) ) {
			return;
		}

		wp_enqueue_script( 'itsec-passwordless-login-profile' );
		wp_enqueue_style( 'itsec-passwordless-login-profile' );
	}

	/**
	 * Render the profile fields for enabling/disabling magic login.
	 *
	 * @param WP_User $user
	 */
	public function render_profile_fields( $user ) {
		?>
		<div id="itsec-passwordless-login-profile-root" data-user="<?php echo esc_attr( $user->ID ); ?>"></div>
		<noscript>
			<div class="notice notice-warning notice-alt below-h2"><p><?php esc_html_e( 'You must enable JavaScript to manage Passwordless Login.', 'it-l10n-ithemes-security-pro' ); ?></p></div>
		</noscript>
		<?php
	}

	/**
	 * Registers the Passwordless Login block.
	 */
	public function register_block() {
		register_block_type_from_metadata( __DIR__ . '/entries/block/block.json', [
			'render_callback' => [ $this, 'render_block' ],
		] );
		add_shortcode( 'itsec_passwordless_login_settings', function () {
			wp_enqueue_script( 'itsec-passwordless-login-front' );
			wp_enqueue_style( 'itsec-passwordless-login-front' );

			return render_block( [
				'blockName'  => 'ithemes-security/passwordless-login-settings',
				'attributes' => [],
				'children'   => [],
			] );
		} );
	}

	/**
	 * Renders the Passwordless Login block.
	 *
	 * @return string
	 */
	public function render_block(): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$user = wp_get_current_user();

		if ( ! ITSEC_Passwordless_Login_Utilities::is_available_for_user( $user ) ) {
			return '';
		}

		return sprintf( '<div id="itsec-passwordless-login-frontend-root" data-user="%d"></div>', $user->ID );
	}

	/**
	 * Register the magic link notification.
	 *
	 * @param array $notifications
	 *
	 * @return array
	 */
	public function register_notification( $notifications ) {
		$notifications[ self::NOTIFICATION ] = array(
			'recipient'        => ITSEC_Notification_Center::R_USER,
			'schedule'         => ITSEC_Notification_Center::S_NONE,
			'subject_editable' => true,
			'message_editable' => true,
			'tags'             => array( 'username', 'display_name', 'login_url', 'site_title', 'site_url' ),
			'module'           => 'passwordless-login',
		);

		return $notifications;
	}

	/**
	 * Register strings for the Magic Links Login Method notification.
	 *
	 * @return array
	 */
	public function notification_strings() {
		return array(
			'label'       => __( 'Passwordless Login', 'it-l10n-ithemes-security-pro' ),
			'description' => sprintf(
				__( 'The %1$sPasswordless Login%2$s module sends an email with a link to automatically login. Note: the default email template already includes the %3$s tag as a button.', 'it-l10n-ithemes-security-pro' ),
				ITSEC_Core::get_link_for_settings_route( ITSEC_Core::get_settings_module_route( 'passwordless-login' ) ),
				'</a>',
				'<code>login_url</code>'
			),
			'tags'        => array(
				'username'     => __( 'The recipient’s WordPress username.', 'it-l10n-ithemes-security-pro' ),
				'display_name' => __( 'The recipient’s WordPress display name.', 'it-l10n-ithemes-security-pro' ),
				'login_url'    => __( 'The magic login link to continue logging in.', 'it-l10n-ithemes-security-pro' ),
				'site_title'   => __( 'The WordPress Site Title. Can be changed under Settings → General → Site Title', 'it-l10n-ithemes-security-pro' ),
				'site_url'     => __( 'The URL to your website.', 'it-l10n-ithemes-security-pro' ),
			),
			'subject'     => __( 'Login Link', 'it-l10n-ithemes-security-pro' ),
			'message'     => __( 'Hi {{ $display_name }},

Click the button below to continue logging in.', 'it-l10n-ithemes-security-pro' ),
		);
	}

	/**
	 * Register Passwordless Login integrations.
	 */
	private function register_integrations() {
		if ( function_exists( 'WC' ) ) {
			require_once( __DIR__ . '/integrations/WooCommerce.php' );
			ITSEC_Passwordless_Login_Integrations::register( 'wc', WooCommerce::class );
		}

		if ( function_exists( 'LLMS' ) ) {
			require_once( __DIR__ . '/integrations/LifterLMS.php' );
			ITSEC_Passwordless_Login_Integrations::register( 'lifter-lms', LifterLMS::class );
		}

		if ( function_exists( 'EDD' ) ) {
			require_once( __DIR__ . '/integrations/EasyDigitalDownloads.php' );
			ITSEC_Passwordless_Login_Integrations::register( 'edd', EasyDigitalDownloads::class );
		}

		if ( function_exists( 'restrict_content_pro' ) ) {
			require_once( __DIR__ . '/integrations/RestrictContentPro.php' );
			ITSEC_Passwordless_Login_Integrations::register( 'rcp', RestrictContentPro::class );
		}
	}

	/**
	 * Adds middleware to API Fetch to exclude the nonce
	 * if no credentials are being passed.
	 *
	 * @return void
	 */
	private function add_api_fetch_middleware() {
		$js = <<<'JS'
wp.apiFetch.use( function createApiFetchSkipNonceMiddleware( options, next ) {
	if ( options.credentials !== 'omit' || ! options.headers ) {
		return next( options );
	}
	const headers = Object.keys( options.headers ).reduce( ( acc, header ) => {
		if ( header.toLowerCase() !== 'x-wp-nonce' ) {
			acc[ header ] = options.headers[ header ];
		}
		return acc;
	}, {} );
	return next( { ...options, headers } );
} );
JS;

		$after = wp_scripts()->get_data( 'wp-api-fetch', 'after' );
		array_unshift( $after, $js );
		wp_scripts()->add_data( 'wp-api-fetch', 'after', $after );
	}
}
