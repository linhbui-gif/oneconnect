<?php
/**
 * WooCommerce Recaptcha Integration.
 *
 * @since   4.1.0
 * @license GPLv2+
 */

/**
 * Class ITSEC_Recaptcha_Integration_WooCommerce
 */
final class ITSEC_Recaptcha_Integration_WooCommerce {

	public function run() {
		if ( is_user_logged_in() ) {
			return;
		}

		if ( ITSEC_Recaptcha_API::is_login_protected() ) {
			add_action( 'woocommerce_login_form', [ $this, 'add_to_login_form' ] );
		}

		if ( ITSEC_Recaptcha_API::is_registration_protected() ) {
			add_action( 'woocommerce_register_form', [ $this, 'add_to_register_form' ] );
			add_action( 'woocommerce_after_checkout_registration_form', [ $this, 'add_to_register_form' ] );
			add_filter( 'woocommerce_process_registration_errors', [ $this, 'validate_register_form' ] );
			add_filter( 'woocommerce_registration_errors', [ $this, 'validate_register_form' ] );
		}

		if ( ITSEC_Recaptcha_API::is_password_reset_protected() ) {
			add_action( 'woocommerce_lostpassword_form', [ $this, 'add_to_reset_pass_form' ] );
		}
	}

	/**
	 * Display the recaptcha on the login form on both the account page and during checkout.
	 */
	public function add_to_login_form() {
		echo ITSEC_Recaptcha_API::render( [ 'action' => ITSEC_Recaptcha::A_LOGIN, 'margin' => [ 'top' => 10, 'bottom' => 10 ] ] );
	}

	/**
	 * Display the recaptcha on the registration form on both the account page and during checkout.
	 */
	public function add_to_register_form() {
		echo ITSEC_Recaptcha_API::render( [ 'action' => ITSEC_Recaptcha::A_REGISTER, 'margin' => [ 'top' => 10, 'bottom' => 10 ] ] );
	}

	/**
	 * Validate the registration form recaptcha.
	 *
	 * @since 4.1.0
	 *
	 * @param WP_Error $error
	 *
	 * @return WP_Error
	 */
	public function validate_register_form( $error ) {
		$result = ITSEC_Recaptcha_API::validate( [ 'action' => ITSEC_Recaptcha::A_REGISTER ] );

		if ( is_wp_error( $result ) ) {
			$error->add( $result->get_error_code(), $result->get_error_message() );
		}

		return $error;
	}

	/**
	 * Adds the recaptcha to the reset password form.
	 */
	public function add_to_reset_pass_form() {
		echo ITSEC_Recaptcha_API::render( [ 'action' => ITSEC_Recaptcha::A_RESET_PASS, 'margin' => [ 'top' => 10, 'bottom' => 10 ] ] );
	}
}
