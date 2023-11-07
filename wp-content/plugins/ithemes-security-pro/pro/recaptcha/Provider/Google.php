<?php

namespace iThemesSecurity\Recaptcha\Provider;

use iThemesSecurity\Contracts\Runnable;
use iThemesSecurity\Lib\Result;

class Google implements Provider, Runnable {

	/** @var array */
	private $settings;

	public function __construct( array $settings ) {
		$this->settings = $settings;
	}

	public function run() {
		if ( 'v3' === $this->settings['type'] && 'everywhere' === $this->settings['v3_include_location'] ) {
			add_action( 'wp_footer', [ $this, 'enqueue_everywhere' ], 19 );
		}
	}

	public function enqueue_everywhere(): void {
		foreach ( wp_scripts()->registered as $handle => $dependency ) {
			if ( ! $dependency instanceof \_WP_Dependency || ! $dependency->src ) {
				continue;
			}

			// Quick check
			if ( false === strpos( $dependency->src, 'google.com/recaptcha/api.js' ) ) {
				continue;
			}

			if ( ! $parsed = parse_url( $dependency->src ) ) {
				continue;
			}

			if ( ! isset( $parsed['host'] ) || ( 'www.google.com' !== $parsed['host'] && 'google.com' !== $parsed['host'] ) ) {
				continue;
			}

			if ( ! isset( $parsed['path'] ) || ( '/recaptcha/api.js' !== $parsed['path'] && 'recaptcha/api.js' !== $parsed['path'] ) ) {
				continue;
			}

			if ( wp_script_is( $handle ) || wp_script_is( $handle, 'done' ) ) {
				return;
			}
		}

		wp_enqueue_script( \ITSEC_Recaptcha::SDK_HANDLE, $this->get_sdk_url( false ), [], '', true );
	}

	public function is_configured(): bool {
		return ! empty( $this->settings['site_key'] ) && ! empty( $this->settings['secret_key'] );
	}

	public function get_html( array $args ): string {
		if ( 'v3' === $this->settings['type'] ) {
			$controlled = $args['controlled'] ? ' data-controlled="true"' : '';

			return sprintf(
				'<input type="hidden" name="g-recaptcha-response" class="itsec-g-recaptcha" data-action="%s"%s>',
				esc_attr( $args['action'] ),
				$controlled
			);
		}

		if ( 'invisible' === $this->settings['type'] ) {
			return sprintf(
				'<div class="g-recaptcha" id="g-recaptcha-%s" data-sitekey="%s" data-size="invisible" data-badge="%s"></div>',
				esc_attr( $args['instance'] ),
				esc_attr( $this->settings['site_key'] ),
				esc_attr( $this->settings['invis_position'] )
			);
		}

		$theme = $this->settings['theme'] ? 'dark' : 'light';
		$style = sprintf(
			'margin:%dpx %dpx %dpx %dpx',
			$args['margin']['top'],
			$args['margin']['right'],
			$args['margin']['bottom'],
			$args['margin']['left']
		);

		return sprintf(
			'<div class="g-recaptcha" id="g-recaptcha-%s" data-sitekey="%s" data-theme="%s" style="%s"></div>',
			esc_attr( $args['instance'] ),
			esc_attr( $this->settings['site_key'] ),
			esc_attr( $theme ),
			esc_attr( $style )
		);
	}

	public function get_opt_in_text(): string {
		return sprintf(
			wp_kses(
			/* Translators: 1: Google's privacy policy URL, 2: Google's terms of use URL */
				__( 'For security, use of Google\'s reCAPTCHA service is required which is subject to the Google <a href="%1$s">Privacy Policy</a> and <a href="%2$s">Terms of Use</a>.', 'it-l10n-ithemes-security-pro' ),
				[ 'a' => [ 'href' => [] ] ]
			),
			'https://policies.google.com/privacy',
			'https://policies.google.com/terms'
		);
	}

	public function get_sdk_url( bool $load_immediately ): string {
		$script = 'https://www.google.com/recaptcha/api.js';

		$query_args = [
			'render' => 'explicit'
		];

		if ( ! empty( $this->settings['language'] ) ) {
			$query_args['hl'] = $this->settings['language'];
		}

		if ( $this->settings['type'] === 'v3' ) {
			$query_args['render'] = $this->settings['site_key'];
		}

		if ( $load_immediately ) {
			$query_args['onload'] = $this->get_js_load_function();
		}

		return add_query_arg( $query_args, $script );
	}

	public function get_js_load_function(): string {
		switch ( $this->settings['type'] ) {
			case 'invisible':
				return 'itsecInvisibleRecaptchaLoad';
			case 'v3':
				return 'itsecRecaptchav3Load';
			case 'v2':
			default:
				return 'itsecRecaptchav2Load';
		}
	}

	public function register_assets(): void {
		if ( 'v3' === $this->settings['type'] ) {
			wp_register_script( \ITSEC_Recaptcha::HANDLE, plugins_url( 'js/recaptcha-v3.js', __DIR__ ), [ 'jquery' ], \ITSEC_Core::get_plugin_build() );
		} elseif ( 'invisible' === $this->settings['type'] ) {
			wp_register_script( \ITSEC_Recaptcha::HANDLE, plugins_url( 'js/invisible-recaptcha.js', __DIR__ ), [ 'jquery' ], \ITSEC_Core::get_plugin_build() );
		} else {
			wp_register_script( \ITSEC_Recaptcha::HANDLE, plugins_url( 'js/recaptcha-v2.js', __DIR__ ), [], \ITSEC_Core::get_plugin_build() );
		}

		wp_localize_script( \ITSEC_Recaptcha::HANDLE, 'itsecRecaptcha', [
			'siteKey' => $this->settings['site_key'],
		] );
	}

	public function validate( array $args ): Result {
		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			return Result::error( new \WP_Error(
				'itsec.recaptcha.form-not-submitted',
				esc_html__( 'You must submit the reCAPTCHA to proceed. Please try again.', 'it-l10n-ithemes-security-pro' ),
				compact( 'args' )
			) );
		}

		$url = add_query_arg(
			[
				'secret'   => $this->settings['secret_key'],
				'response' => esc_attr( $_POST['g-recaptcha-response'] ),
				'remoteip' => \ITSEC_Lib::get_ip(),
			],
			'https://www.google.com/recaptcha/api/siteverify'
		);

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			$http_error = new \WP_Error(
				'itsec.recaptcha.http-error',
				__( 'Cannot connect to Google reCAPTCHA API.', 'it-l10n-ithemes-security-pro' )
			);
			$http_error->merge_from( $response );

			return Result::error( $http_error );
		}

		$status = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! $this->is_valid_response_format( $status ) ) {
			return Result::error( new \WP_Error(
				'itsec.recaptcha.invalid-response',
				__( 'The reCAPTCHA API returned an invalid response format.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		$validation_error = $this->validate_response( $status, $args );

		if ( ! $validation_error ) {
			return Result::success( true );
		}

		if ( ! $status['error-codes'] ) {
			return Result::error( $validation_error );
		}

		if ( [ 'invalid-input-secret' ] === $status['error-codes'] ) {
			return Result::error( new \WP_Error(
				'itsec.recaptcha.invalid-api-keys',
				__( 'The reCAPTCHA API keys are invalid.', 'it-l10n-ithemes-security-pro' ),
				[ 'report_to_admin' => true ]
			) );
		}

		foreach ( $status['error-codes'] as $error_code ) {
			$validation_error->add(
				sprintf( 'itsec.recaptcha.google.%s', $error_code ),
				sprintf( __( 'The reCAPTCHA server reported the following error: %s.', 'it-l10n-ithemes-security-pro' ), $error_code ),
				[ 'report_to_admin' => $error_code !== 'timeout-or-duplicate' ]
			);
		}

		return Result::error( $validation_error );
	}

	/**
	 * Is the reCAPTCHA response from Google valid.
	 *
	 * @param mixed $response
	 *
	 * @return bool
	 */
	private function is_valid_response_format( $response ): bool {
		if ( ! is_array( $response ) ) {
			return false;
		}

		if ( ! isset( $response['success'] ) ) {
			return false;
		}

		if ( 'v3' === $this->settings['type'] && ! isset( $response['score'], $response['action'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Validate the response.
	 *
	 * @param array $response The response from Google.
	 * @param array $args     The args passed by the user.
	 *
	 * @return \WP_Error|null
	 */
	private function validate_response( array $response, array $args ): ?\WP_Error {
		\ITSEC_Log::add_debug( 'recaptcha', 'validate-response', compact( 'response', 'args' ) );

		$error = new \WP_Error(
			'itsec.recaptcha.incorrect',
			esc_html__( 'The captcha response you submitted does not appear to be valid. Please try again.', 'it-l10n-ithemes-security-pro' )
		);

		if ( ! $response['success'] ) {
			$error->add_data( [ 'validate_error' => 'invalid-token', 'args' => $args ] );

			return $error;
		}

		if ( ! $this->validate_host( $response ) ) {
			$error->add_data( [ 'validate_error' => 'host-mismatch', 'args' => $args ] );

			return $error;
		}

		if ( ! $this->validate_action( $response, $args ) ) {
			$error->add_data( [ 'validate_error' => 'action-mismatch', 'args' => $args ] );

			return $error;
		}

		if ( ! $this->validate_score( $response, $args ) ) {
			$error->add_data( [ 'validate_error' => 'insufficient_score', 'args' => $args ] );

			return $error;
		}

		return null;
	}

	/**
	 * Validate the hostname the Recaptcha was filled on.
	 *
	 * This allows the user to disable "Domain Name Validation" on large multisite installations because Google
	 * limits the number of sites a recaptcha key can be used on.
	 *
	 * @since 4.2.0
	 *
	 * @param array $status
	 *
	 * @return bool
	 */
	private function validate_host( array $status ): bool {
		if ( ! apply_filters( 'itsec_recaptcha_validate_host', false ) ) {
			return true;
		}

		if ( ! isset( $status['hostname'] ) ) {
			return true;
		}

		$site_parsed = parse_url( site_url() );

		if ( ! is_array( $site_parsed ) || ! isset( $site_parsed['host'] ) ) {
			return true;
		}

		return $site_parsed['host'] === $status['hostname'];
	}

	/**
	 * Validate that the action matches.
	 *
	 * @param array $status Response from Google.
	 * @param array $args   Validation args.
	 *
	 * @return bool
	 */
	private function validate_action( array $status, array $args ): bool {
		if ( 'v3' !== $this->settings['type'] ) {
			return true;
		}

		return empty( $args['action'] ) || $status['action'] === $args['action'];
	}

	/**
	 * Validate that the score is above the threshold.
	 *
	 * @param array $status Response from Google.
	 * @param array $args   Validation args.
	 *
	 * @return bool
	 */
	private function validate_score( array $status, array $args ): bool {
		if ( 'v3' !== $this->settings['type'] ) {
			return true;
		}

		$threshold = $args['v3_threshold'] ?? $this->settings['v3_threshold'];

		return $status['score'] >= $threshold;
	}
}
