<?php

class ITSEC_Recaptcha_Settings extends \iThemesSecurity\Config_Settings {

	public function get_settings_schema() {
		$schema = parent::get_settings_schema();

		if ( $this->is_rcp_enabled() ) {
			$schema['properties']['provider']['description'] .=
				' ' . __( 'iThemes Security has detected that Restrict Content Pro is installed.', 'it-l10n-ithemes-security-pro' ) .
				' ' . __( 'Google reCAPTCHA v3 is the only supported provider when using Restrict Content Pro.', 'it-l10n-ithemes-security-pro' );

			$schema['properties']['provider']['oneOf'] = array_filter(
				$schema['properties']['provider']['oneOf'],
				function ( $oneOf ) {
					return $oneOf['enum'][0] === 'google';
				}
			);
		}

		return $schema;
	}

	public function load() {
		parent::load();

		if ( $this->is_rcp_enabled() ) {
			$this->settings['provider'] = 'google';
		}
	}

	protected function is_rcp_enabled(): bool {
		return function_exists( 'restrict_content_pro' );
	}
}

ITSEC_Modules::register_settings( new ITSEC_Recaptcha_Settings( ITSEC_Modules::get_config( 'recaptcha' ) ) );
