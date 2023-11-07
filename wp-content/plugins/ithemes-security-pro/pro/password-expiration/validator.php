<?php

use iThemesSecurity\Config_Validator;

class ITSEC_Password_Expiration_Validator extends Config_Validator {
	protected function sanitize_settings() {
		parent::sanitize_settings();

		if ( ! empty( $this->settings['expire_force'] ) ) {
			$this->settings['expire_force'] = ITSEC_Core::get_current_time_gmt();
		} elseif ( false === $this->settings['expire_force'] ) {
			$this->settings['expire_force'] = 0;
			ITSEC_Lib_Password_Requirements::global_clear_required_password_change( 'force' );
		} else {
			$this->settings['expire_force'] = $this->previous_settings['expire_force'];
		}
	}
}

ITSEC_Modules::register_validator( new ITSEC_Password_Expiration_Validator( ITSEC_Modules::get_config( 'password-expiration' ) ) );
