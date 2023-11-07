<?php

namespace iThemesSecurity\Recaptcha\Provider;

use iThemesSecurity\Lib\Result;

class hCaptcha implements Provider {
	private const IGNORED_ERRORS = [
		'invalid-input-response',
		'invalid-or-already-seen-response',
	];

	/** @var array */
	private $settings;

	public function __construct( array $settings ) { $this->settings = $settings; }

	public function is_configured(): bool {
		return $this->settings['hc_site_key'] && $this->settings['hc_secret_key'];
	}

	public function get_html( array $args ): string {
		$style = sprintf(
			'margin:%dpx %dpx %dpx %dpx',
			$args['margin']['top'],
			$args['margin']['right'],
			$args['margin']['bottom'],
			$args['margin']['left']
		);

		return sprintf(
			'<div class="itsec-h-captcha" id="itsec-h-captcha-%d" style="%s"></div>',
			esc_attr( $args['instance'] ),
			esc_attr( $style )
		);
	}

	public function get_opt_in_text(): string {
		return sprintf(
			wp_kses(
			/* Translators: 1: Privacy policy URL, 2: Terms of use URL */
				__( 'For security, use of hCaptcha is required which is subject to their <a href="%1$s">Privacy Policy</a> and <a href="%2$s">Terms of Use</a>.', 'it-l10n-ithemes-security-pro' ),
				[ 'a' => [ 'href' => [] ] ]
			),
			'https://www.hcaptcha.com/privacy',
			'https://www.hcaptcha.com/terms'
		);
	}

	public function register_assets(): void {
		wp_register_script( \ITSEC_Recaptcha::HANDLE, plugins_url( 'js/hcaptcha.js', __DIR__ ), [ 'jquery' ], \ITSEC_Core::get_plugin_build() );
		wp_localize_script( \ITSEC_Recaptcha::HANDLE, 'itsecRecaptcha', [
			'config' => [
				'sitekey' => $this->settings['hc_site_key'],
				'theme'   => $this->settings['hc_theme'],
				'size'    => $this->settings['hc_size'],
			],
		] );
	}

	public function get_sdk_url( bool $load_immediately ): string {
		$script     = 'https://js.hcaptcha.com/1/api.js';
		$query_args = [
			'render'          => 'explicit',
			'recaptchacompat' => 'off',
		];

		if ( $load_immediately ) {
			$query_args['onload'] = $this->get_js_load_function();
		}

		return add_query_arg( $query_args, $script );
	}

	public function get_js_load_function(): string {
		return 'itsecHCaptchaLoad';
	}

	public function validate( array $args ): Result {
		if ( empty( $_POST['h-captcha-response'] ) ) {
			return Result::error( new \WP_Error(
				'itsec.recaptcha.form-not-submitted',
				esc_html__( 'You must submit the CAPTCHA to proceed. Please try again.', 'it-l10n-ithemes-security-pro' ),
				compact( 'args' )
			) );
		}

		$response = wp_remote_post( 'https://hcaptcha.com/siteverify', [
			'body' => [
				'sitekey'  => $this->settings['hc_site_key'],
				'secret'   => $this->settings['hc_secret_key'],
				'response' => $_POST['h-captcha-response'],
				'remoteip' => \ITSEC_Lib::get_ip(),
			]
		] );

		if ( is_wp_error( $response ) ) {
			$http_error = new \WP_Error(
				'itsec.recaptcha.http-error',
				__( 'Cannot connect to hCaptcha API.', 'it-l10n-ithemes-security-pro' )
			);
			$http_error->merge_from( $response );

			return Result::error( $http_error );
		}

		$body   = wp_remote_retrieve_body( $response );
		$status = json_decode( $body, true );

		if ( ! $this->is_valid_response_format( $status ) ) {
			return Result::error( new \WP_Error(
				'itsec.recaptcha.invalid-response',
				__( 'The hCaptcha API returned an invalid response format.', 'it-l10n-ithemes-security-pro' ),
				[ 'response' => $body ]
			) );
		}

		$validation_error = $this->validate_response( $status, $args );

		if ( ! $validation_error ) {
			return Result::success( true );
		}

		if ( [ 'invalid-input-secret' ] === $status['error-codes'] ) {
			return Result::error( new \WP_Error(
				'itsec.recaptcha.invalid-api-keys',
				__( 'The hCaptcha API keys are invalid.', 'it-l10n-ithemes-security-pro' ),
				[ 'report_to_admin' => true ]
			) );
		}

		foreach ( $status['error-codes'] as $error_code ) {
			$validation_error->add(
				sprintf( 'itsec.recaptcha.hcaptcha.%s', $error_code ),
				sprintf( __( 'The hCaptcha server reported the following error: %s.', 'it-l10n-ithemes-security-pro' ), $error_code ),
				[ 'report_to_admin' => ! in_array( $error_code, self::IGNORED_ERRORS, true ) ]
			);
		}

		return Result::error( $validation_error );
	}

	/**
	 * Is the CAPTCHA response from CloudFlare valid.
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

		return true;
	}

	/**
	 * Validate the response.
	 *
	 * @param array $response The response from hCaptcha.
	 * @param array $args     The args passed by the user.
	 *
	 * @return \WP_Error|null
	 */
	private function validate_response( array $response, array $args ): ?\WP_Error {
		\ITSEC_Log::add_debug( 'recaptcha', 'validate-response', compact( 'response', 'args' ) );

		$error = new \WP_Error(
			'itsec.recaptcha.incorrect',
			esc_html__( 'The CAPTCHA you submitted does not appear to be valid. Please try again.', 'it-l10n-ithemes-security-pro' )
		);

		if ( ! $response['success'] ) {
			$error->add_data( [ 'validate_error' => 'invalid-token', 'args' => $args ] );

			return $error;
		}

		return null;
	}
}
