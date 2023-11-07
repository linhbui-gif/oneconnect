<?php

class ITSEC_Passwordless_Login_Settings extends \iThemesSecurity\Config_Settings {

	public function get_settings_schema() {
		$schema = parent::get_settings_schema();

		$integrations = ITSEC_Passwordless_Login_Integrations::get_integrations();

		if ( $integrations ) {
			foreach ( $integrations as $integration ) {
				$schema['properties']['integrations']['properties'][ $integration->get_slug() ]          = $schema['properties']['integrations']['additionalProperties'];
				$schema['properties']['integrations']['properties'][ $integration->get_slug() ]['title'] = $integration->get_name();
			}
		} else {
			$schema['uiSchema']['integrations']['ui:widget'] = 'hidden';
		}

		return $schema;
	}
}

ITSEC_Modules::register_settings( new ITSEC_Passwordless_Login_Settings( ITSEC_Modules::get_config( 'passwordless-login' ) ) );
