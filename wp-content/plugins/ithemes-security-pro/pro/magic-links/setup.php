<?php

class ITSEC_Magic_Links_Setup {

	public function __construct() {
		add_action( 'itsec_modules_do_plugin_upgrade', array( $this, 'execute_upgrade' ) );
	}

	/**
	 * Execute module upgrade
	 *
	 * @param int $itsec_old_version
	 */
	public function execute_upgrade( $itsec_old_version ) {
		if ( $itsec_old_version < 4123 && ITSEC_Modules::is_active( 'magic-links' ) && empty( ITSEC_Storage::get( 'magic-links' )['lockout_bypass'] ) ) {
			ITSEC_Modules::deactivate( 'magic-links' );
		}
	}
}

new ITSEC_Magic_Links_Setup();
