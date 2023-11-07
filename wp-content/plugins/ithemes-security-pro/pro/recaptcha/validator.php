<?php

use iThemesSecurity\Config_Validator;

class ITSEC_Recaptcha_Validator extends Config_Validator {

	private const REVALIDATE_SETTINGS = [
		'provider',
		'type',
		'site_key',
		'secret_key',
		'cf_site_key',
		'cf_secret_key',
	];

	protected function sanitize_settings() {
		parent::sanitize_settings();

		foreach ( self::REVALIDATE_SETTINGS as $setting ) {
			if ( $this->settings[ $setting ] !== $this->previous_settings[ $setting ] ) {
				$this->settings['validated']  = false;
				$this->settings['last_error'] = '';
				break;
			}
		}
	}

	protected function validate_settings() {
		parent::validate_settings();

		if ( ! $this->can_save() ) {
			return;
		}

		if ( ITSEC_Core::doing_data_upgrade() ) {
			return;
		}

		if ( $this->settings['provider'] === 'google' && ( ! $this->settings['site_key'] || ! $this->settings['secret_key'] ) ) {
			$this->add_error( new WP_Error(
				'itsec.recaptcha.missing-keys',
				esc_html__( 'The Site Key and Secret Key are required.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		if ( $this->settings['provider'] === 'cloudflare' && ( ! $this->settings['cf_site_key'] || ! $this->settings['cf_secret_key'] ) ) {
			$this->add_error( new WP_Error(
				'itsec.recaptcha.missing-keys',
				esc_html__( 'The Site Key and Secret Key are required.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		if ( $this->settings['provider'] === 'hcaptcha' && ( ! $this->settings['hc_site_key'] || ! $this->settings['hc_secret_key'] ) ) {
			$this->add_error( new WP_Error(
				'itsec.recaptcha.missing-keys',
				esc_html__( 'The Site Key and Secret Key are required.', 'it-l10n-ithemes-security-pro' )
			) );
		}
	}
}

ITSEC_Modules::register_validator( new ITSEC_Recaptcha_Validator( ITSEC_Modules::get_config( 'recaptcha' ) ) );
