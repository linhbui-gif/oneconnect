<?php

use iThemesSecurity\Contracts\Runnable;
use iThemesSecurity\Lib\Password_Requirement;

class ITSEC_Password_Expiration implements Runnable {

	/** @var Password_Requirement[] */
	private $requirements;

	/**
	 * ITSEC_Password_Expiration constructor.
	 *
	 * @param Password_Requirement ...$requirements
	 */
	public function __construct( Password_Requirement ...$requirements ) { $this->requirements = $requirements; }

	public function run() {
		add_action( 'itsec_register_password_requirements', [ $this, 'register_requirements' ] );

		add_action( 'itsec_password_requirements_enqueue_scripts_and_styles', [ $this, 'enqueue_force_scripts' ] );
		add_action( 'itsec_password_requirements_settings_before', [ $this, 'render_force_button' ] );
		add_action( 'itsec_password_requirements_ajax_force', [ $this, 'handle_force_button' ] );
	}

	public function register_requirements() {
		array_walk( $this->requirements, [ ITSEC_Lib_Password_Requirements::class, 'register' ] );
	}

	public function enqueue_force_scripts() {
		wp_enqueue_script( 'itsec-password-expiration-settings', plugin_dir_url( __FILE__ ) . 'js/settings-page.js', array( 'jquery', 'itsec-util' ), ITSEC_Core::get_plugin_build() );
	}

	/**
	 * Render the force password change AJAX button.
	 *
	 * @param ITSEC_Form $form
	 */
	public function render_force_button( $form ) {
		?>
		<div class="itsec-password-requirements-password-expiration-force">
			<p><?php _e( 'Press the button below to force all users to change their password upon their next login.', 'it-l10n-ithemes-security-pro' ); ?></p>
			<p><?php $form->add_button( 'force-expiration', array( 'value' => esc_html__( 'Force Password Change', 'it-l10n-ithemes-security-pro' ), 'class' => 'button' ) ); ?></p>
			<div id="itsec_password_expiration_undo"><?php echo $this->get_force_in_effect_notice(); ?></div>
			<div id="itsec_password_expiration_status"></div>
		</div>
		<?php
	}

	/**
	 * Get the notice whether
	 *
	 * @return string
	 */
	private function get_force_in_effect_notice() {

		if ( ! $force = ITSEC_Modules::get_setting( 'password-expiration', 'expire_force' ) ) {
			return '';
		}

		$html = '<p>';
		$html .= sprintf(
			esc_html__( 'Passwords created before %1$s are required to be reset. %2$sUndo force password change%3$s.', 'it-l10n-ithemes-security-pro' ),
			ITSEC_Lib::date_format_i18n_and_local_timezone( $force ),
			'<button class="button-link" id="itsec-password-requirements-force-expiration-undo">',
			'</button>'
		);
		$html .= '</p>';

		return $html;
	}

	/**
	 * Handle the force reset button request.
	 *
	 * @param array $data
	 */
	public function handle_force_button( $data ) {
		if ( 'force-expiration' === $data['method'] ) {
			$response = ITSEC_Modules::set_setting( 'password-expiration', 'expire_force', true );

			if ( is_wp_error( $response ) ) {
				ITSEC_Response::add_error( $response );
			} elseif ( $response['saved'] ) {
				ITSEC_Response::add_message( esc_html__( 'Passwords will be reset on next login.', 'it-l10n-ithemes-security-pro' ) );
				ITSEC_Response::set_response( $this->get_force_in_effect_notice() );
			}
		} elseif ( 'force-expiration-undo' === $data['method'] ) {
			$response = ITSEC_Modules::set_setting( 'password-expiration', 'expire_force', false );

			if ( is_wp_error( $response ) ) {
				ITSEC_Response::add_error( $response );
			} elseif ( $response['saved'] ) {
				ITSEC_Response::add_message( esc_html__( 'Passwords reset is no longer required.', 'it-l10n-ithemes-security-pro' ) );
				ITSEC_Response::set_response( $this->get_force_in_effect_notice() );
			}
		}
	}
}
