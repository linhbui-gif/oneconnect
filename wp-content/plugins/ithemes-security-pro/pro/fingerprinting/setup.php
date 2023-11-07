<?php

use iThemesSecurity\User_Groups\Upgrader;

class ITSEC_Fingerprinting_Setup {

	public function __construct() {
		add_action( 'itsec_modules_do_plugin_upgrade', array( $this, 'execute_upgrade' ) );
	}

	/**
	 * Execute module upgrade
	 *
	 * @param int $itsec_old_version
	 *
	 * @return void
	 */
	public function execute_upgrade( $itsec_old_version ) {
		if ( $itsec_old_version < 4117 ) {
			$upgrader = ITSEC_Modules::get_container()->get( Upgrader::class );
			ITSEC_Modules::set_setting(
				'fingerprinting',
				'group',
				$upgrader->upgrade_from_min_role(
					ITSEC_Modules::get_setting( 'fingerprinting', 'role' ) ?: 'subscriber'
				)
			);
		}

		if ( $itsec_old_version < 4123 ) {
			$fingerprinting = ITSEC_Modules::get_settings( 'fingerprinting' );
			$geolocation    = [
				'mapbox_access_token' => $fingerprinting['mapbox_access_token'] ?? '',
				'mapquest_api_key'    => $fingerprinting['mapquest_api_key'] ?? '',
			];

			$geolocation['maxmind_lite'] = [
				'key' => $fingerprinting['maxmind_lite_key'] ?? '',
			];
			$geolocation['maxmind_api']  = [
				'user' => $fingerprinting['maxmind_api_user'] ?? '',
				'key'  => $fingerprinting['maxmind_api_key'] ?? '',
			];

			if ( $geolocation ) {
				ITSEC_Modules::set_settings( 'geolocation', $geolocation );
			}

			ITSEC_Modules::set_settings( 'fingerprinting', array_diff_key( $fingerprinting, [
				'maxmind_lite_key',
				'maxmind_api_user',
				'maxmind_api_key',
				'mapbox_access_token',
				'mapquest_api_key'
			] ) );
		}
	}
}

new ITSEC_Fingerprinting_Setup();
