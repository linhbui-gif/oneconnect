<?php

namespace iThemesSecurity\Modules\Password_Expiration;

use iThemesSecurity\Lib\Config_Password_Requirement;

final class Force_Requirement extends Config_Password_Requirement {
	public function is_password_change_required( \WP_User $user, array $settings ): bool {
		$expire_force = \ITSEC_Modules::get_setting( $this->get_module(), 'expire_force' );

		if ( $expire_force <= 0 ) {
			return false;
		}

		return \ITSEC_Lib_Password_Requirements::password_last_changed( $user ) <= $expire_force;
	}

	public function evaluate( string $password, $user ) {
		return new \WP_Error( 'not_implemented', __( 'This password requirement does not evaluate passwords.', 'it-l10n-ithemes-security-pro' ) );
	}

	public function validate( $evaluation, $user, array $settings, array $args ) {
		return true;
	}

	public function get_reason_message( $evaluation, array $settings ): string {
		return esc_html__( 'An admin has required you to reset your password.', 'it-l10n-ithemes-security-pro' );
	}

	public function is_always_enabled(): bool {
		return true;
	}

	public function should_evaluate_if_not_enabled(): bool {
		return false;
	}

	public function render( \ITSEC_Form $form ) {

	}
}
