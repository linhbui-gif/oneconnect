<?php

namespace iThemesSecurity\Recaptcha\Provider;

use iThemesSecurity\Lib\Result;

class CloudFlare implements Provider {

	private const IGNORED_ERRORS = [
		'timeout-or-duplicate',
		'invalid-input-response',
	];

	/** @var array */
	private $settings;

	public function __construct( array $settings ) { $this->settings = $settings; }

	public function is_configured(): bool {
		return ! empty( $this->settings['cf_site_key'] ) && ! empty( $this->settings['cf_secret_key'] );
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
			'<div class="itsec-cf-turnstile" id="itsec-cf-turnstile-%d" style="%s" data-action="%s"></div>',
			esc_attr( $args['instance'] ),
			esc_attr( $style ),
			esc_attr( $args['action'] )
		);
	}

	public function get_opt_in_text(): string {
		return sprintf(
			wp_kses(
			/* Translators: 1: CloudFlare's privacy policy URL, 2: CloudFlare's terms of use URL */
				__( 'For security, use of CloudFlare\'s Turnstile service is required which is subject to the CloudFlare <a href="%1$s">Privacy Policy</a> and <a href="%2$s">Terms of Use</a>.', 'it-l10n-ithemes-security-pro' ),
				[ 'a' => [ 'href' => [] ] ]
			),
			'https://www.cloudflare.com/privacypolicy/',
			'https://www.cloudflare.com/website-terms/'
		);
	}

	public function register_assets(): void {
		wp_register_script( \ITSEC_Recaptcha::HANDLE, plugins_url( 'js/turnstile.js', __DIR__ ), [ 'jquery' ], \ITSEC_Core::get_plugin_build() );
		wp_localize_script( \ITSEC_Recaptcha::HANDLE, 'itsecRecaptcha', [
			'config' => [
				'sitekey' => $this->settings['cf_site_key'],
				'theme'   => $this->settings['cf_theme'],
				'size'    => $this->settings['cf_size'],
			],
		] );
	}

	public function get_sdk_url( bool $load_immediately ): string {
		$script     = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
		$query_args = [
			'render' => 'explicit',
		];

		if ( $load_immediately ) {
			$query_args['onload'] = $this->get_js_load_function();
		}

		return add_query_arg( $query_args, $script );
	}

	public function get_js_load_function(): string {
		return 'itsecCloudFlareTurnstileLoad';
	}

	public function validate( array $args ): Result {
		if ( empty( $_POST['cf-turnstile-response'] ) ) {
			return Result::error( new \WP_Error(
				'itsec.recaptcha.form-not-submitted',
				esc_html__( 'You must submit the CAPTCHA to proceed. Please try again.', 'it-l10n-ithemes-security-pro' ),
				compact( 'args' )
			) );
		}

		$response = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', [
			'body' => [
				'secret'   => $this->settings['cf_secret_key'],
				'response' => $_POST['cf-turnstile-response'],
				'remoteip' => \ITSEC_Lib::get_ip(),
			]
		] );

		if ( is_wp_error( $response ) ) {
			$http_error = new \WP_Error(
				'itsec.recaptcha.http-error',
				__( 'Cannot connect to CloudFlare Turnstile API.', 'it-l10n-ithemes-security-pro' )
			);
			$http_error->merge_from( $response );

			return Result::error( $http_error );
		}

		$body   = wp_remote_retrieve_body( $response );
		$status = json_decode( $body, true );

		if ( ! $this->is_valid_response_format( $status ) ) {
			return Result::error( new \WP_Error(
				'itsec.recaptcha.invalid-response',
				__( 'The CloudFlare Turnstile API returned an invalid response format.', 'it-l10n-ithemes-security-pro' ),
				[ 'response' => $body ]
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
				__( 'The CloudFlare API keys are invalid.', 'it-l10n-ithemes-security-pro' ),
				[ 'report_to_admin' => true ]
			) );
		}

		foreach ( $status['error-codes'] as $error_code ) {
			$validation_error->add(
				sprintf( 'itsec.recaptcha.cloudflare.%s', $error_code ),
				sprintf( __( 'The CloudFlare Turnstile server reported the following error: %s.', 'it-l10n-ithemes-security-pro' ), $error_code ),
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

		if ( $response['success'] && ! isset( $response['action'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Validate the response.
	 *
	 * @param array $response The response from CloudFlare.
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

		if ( ! $this->validate_action( $response, $args ) ) {
			$error->add_data( [ 'validate_error' => 'action-mismatch', 'args' => $args ] );

			return $error;
		}

		return null;
	}

	/**
	 * Validates that the action matches.
	 *
	 * @param array $status Response from CloudFlare.
	 * @param array $args   Validation args.
	 *
	 * @return bool
	 */
	private function validate_action( array $status, array $args ): bool {
		return empty( $args['action'] ) || $status['action'] === $args['action'];
	}
}
