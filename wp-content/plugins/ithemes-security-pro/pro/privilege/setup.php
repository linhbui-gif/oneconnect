<?php

class ITSEC_Privilege_Setup {
	public function __construct() {
		add_action( 'itsec_modules_do_plugin_uninstall', array( $this, 'execute_uninstall' ) );
		add_action( 'itsec_modules_do_plugin_upgrade', array( $this, 'execute_upgrade' ) );
	}

	/**
	 * Execute module uninstall
	 *
	 * @return void
	 */
	public function execute_uninstall() {
		delete_site_option( 'itsec_privilege' );
	}

	/**
	 * Execute module upgrade
	 *
	 * @param int $itsec_old_version
	 */
	public function execute_upgrade( $itsec_old_version ) {
		if ( $itsec_old_version < 4041 ) {
			$current_options = get_site_option( 'itsec_privilege' );

			// If there are no current options, go with the new defaults by not saving anything
			if ( is_array( $current_options ) ) {
				// Make sure the new module is properly activated or deactivated
				if ( $current_options['enabled'] ) {
					ITSEC_Modules::activate( 'privilege' );
				} else {
					ITSEC_Modules::deactivate( 'privilege' );
				}
			}
		}

		if ( $itsec_old_version < 4068 ) {
			delete_site_option( 'itsec_privilege' );
		}
	}
}

new ITSEC_Privilege_Setup();
