<?php

class ITSEC_Geolocation_Setup {

	public function __construct() {
		add_action( 'itsec_modules_do_plugin_upgrade', array( $this, 'execute_upgrade' ), 11 );
	}

	/**
	 * Execute module upgrade
	 *
	 * @param int $old The old build number.
	 */
	public function execute_upgrade( $old ) {
		$initial = ITSEC_Modules::get_setting( 'global', 'initial_build' );

		if ( $old < 4124 && $initial !== 4123 ) {
			$settings = ITSEC_Modules::get_settings( 'geolocation' );
			$defaults = ITSEC_Modules::get_defaults( 'geolocation' );

			// Using a loose comparison because we want to allow any order.
			if ( $settings != $defaults ) {
				ITSEC_Modules::activate( 'geolocation' );
			}
		}
	}
}

new ITSEC_Geolocation_Setup();
