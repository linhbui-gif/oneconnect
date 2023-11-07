<?php

final class ITSEC_Recaptcha_Integration_LifterLMS {

	public function run() {
		if ( is_user_logged_in() ) {
			return;
		}

		add_action( 'itsec_failed_recaptcha', function ( $error ) {
			add_filter( 'lifterlms_user_login_errors', function () use ( $error ) {
				return $error;
			} );
		} );

		if ( ITSEC_Recaptcha_API::is_login_protected() ) {
			add_filter( 'lifterlms_person_login_fields', [ $this, 'add_to_login_form' ] );
		}

		if ( ITSEC_Recaptcha_API::is_registration_protected() ) {
			add_filter( 'lifterlms_get_person_fields', [ $this, 'add_to_register_form' ], 10, 2 );
			add_filter( 'lifterlms_user_registration_data', [ $this, 'validate_register_form' ], 10, 3 );
		}

		if ( ITSEC_Recaptcha_API::is_password_reset_protected() ) {
			add_filter( 'lifterlms_lost_password_fields', [ $this, 'add_to_reset_pass_form' ] );
			add_filter( 'allow_password_reset', [ $this, 'validate_reset_pass_form' ] );
		}
	}

	/**
	 * Display the recaptcha in the Lifter login form.
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function add_to_login_form( $fields ) {
		$new_fields = [];
		$added      = false;

		$recaptcha_field = [
			'columns'     => 12,
			'id'          => 'itsec_llms_recaptcha',
			'type'        => 'html',
			'description' => ITSEC_Recaptcha_API::render( [ 'action' => ITSEC_Recaptcha::A_LOGIN, 'margin' => [ 'top' => 10, 'bottom' => 10 ] ] ),
		];

		foreach ( $fields as $field ) {
			if ( $field['id'] === 'llms_login_button' ) {
				$new_fields[] = $recaptcha_field;
				$added        = true;
			}

			$new_fields[] = $field;
		}

		if ( ! $added ) {
			$new_fields[] = $recaptcha_field;
		}

		return $new_fields;
	}

	/**
	 * Display the recaptcha on the registration form on both the account page and during checkout.
	 *
	 * @param array  $fields
	 * @param string $screen
	 *
	 * @return array
	 */
	public function add_to_register_form( $fields, $screen ) {
		if ( $screen === 'registration' || $screen === 'checkout' ) {
			$fields[] = [
				'columns'     => 12,
				'id'          => 'itsec_llms_recaptcha',
				'type'        => 'html',
				'description' => ITSEC_Recaptcha_API::render( [ 'action' => ITSEC_Recaptcha::A_REGISTER, 'margin' => [ 'top' => 10, 'bottom' => 10 ] ] ),
			];
		}

		return $fields;
	}

	/**
	 * Validate the registration form recaptcha.
	 *
	 * @since 4.1.0
	 *
	 * @param WP_Error $error
	 * @param array    $data
	 * @param string   $screen
	 *
	 * @return \WP_Error
	 */
	public function validate_register_form( $error, $data, $screen ) {

		if ( is_wp_error( $error ) ) {
			return $error;
		}

		if ( 'registration' !== $screen && 'checkout' !== $screen ) {
			return $error;
		}

		$result = ITSEC_Recaptcha_API::validate( [ 'action' => ITSEC_Recaptcha::A_REGISTER ] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $error;
	}

	/**
	 * Adds the recaptcha in the Lifter reset password form.
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function add_to_reset_pass_form( $fields ) {
		$new_fields = [];
		$added      = false;

		$recaptcha_field = [
			'columns'     => 12,
			'id'          => 'itsec_llms_recaptcha',
			'type'        => 'html',
			'description' => ITSEC_Recaptcha_API::render( [ 'action' => ITSEC_Recaptcha::A_RESET_PASS, 'margin' => [ 'top' => 10, 'bottom' => 10 ] ] ),
		];

		foreach ( $fields as $field ) {
			if ( $field['id'] === 'llms_login' ) {
				$new_fields[] = $recaptcha_field;
				$added        = true;
			}

			$new_fields[] = $field;
		}

		if ( ! $added ) {
			$new_fields[] = $recaptcha_field;
		}

		return $new_fields;
	}

	/**
	 * Validates the recaptcha in the reset pass form.
	 *
	 * @param bool|WP_Error $allowed
	 *
	 * @return bool|WP_Error
	 */
	public function validate_reset_pass_form( $allowed ) {
		if ( true !== $allowed ) {
			return $allowed;
		}

		if ( ! empty( $_POST['_lost_password_nonce'] ) && ! empty( $_POST['llms_login'] ) ) {
			$validated = ITSEC_Recaptcha_API::validate( [ 'action' => ITSEC_Recaptcha::A_RESET_PASS ] );

			if ( is_wp_error( $validated ) ) {
				return $validated;
			}
		}

		return $allowed;
	}
}
