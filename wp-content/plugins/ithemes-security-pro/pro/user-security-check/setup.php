<?php

class ITSEC_User_Security_Check_Setup {

	public function __construct() {
		add_action( 'itsec_modules_do_plugin_upgrade', array( $this, 'execute_upgrade' ) );
	}

	/**
	 * Execute module upgrade
	 *
	 * @param int $old
	 * @param int $new
	 *
	 * @return void
	 */
	public function execute_upgrade( $old ) {
		if ( $old < 4079 ) {
			wp_clear_scheduled_hook( 'itsec_check_inactive_accounts' );
		}
	}
}

new ITSEC_User_Security_Check_Setup();
