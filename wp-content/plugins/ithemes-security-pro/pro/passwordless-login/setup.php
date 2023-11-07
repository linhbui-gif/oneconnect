<?php

use iThemesSecurity\User_Groups\Upgrader;
use iThemesSecurity\User_Groups\User_Group;
use iThemesSecurity\User_Groups;

class ITSEC_Passwordless_Login_Setup {

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
			$settings = ITSEC_Modules::get_settings( 'passwordless-login' );
			$upgrader = ITSEC_Modules::get_container()->get( Upgrader::class );

			switch ( $settings['login'] ?? null ) {
				case 'all':
					$login_group = $upgrader->get_groups_for_all_users();
					break;
				case 'non_privileged':
					$login_group = [
						$upgrader->get_default_group_id( 'subscriber' ),
					];
					break;
				case 'custom':
					if ( ! ( $settings['roles'] ?? null ) ) {
						$login_group = [];
						break;
					}

					$login_group = [
						$upgrader->find_or_create( __( 'Passwordless Login', 'it-l10n-ithemes-security-pro' ), static function ( User_Group $user_group ) use ( $settings ) {
							$user_group->set_roles( ITSEC_Lib::sanitize_roles( $settings['roles'] ) );
						} )->get_id(),
					];
					break;
				default:
					$login_group = [];
					break;
			}

			ITSEC_Modules::set_setting( 'passwordless-login', 'group', $login_group );

			switch ( $settings['2fa_bypass'] ?? null ) {
				case 'all':
					$bypass_group = $upgrader->get_groups_for_all_users();
					break;
				case 'non_privileged':
					$bypass_group = [
						$upgrader->get_default_group_id( 'subscriber' ),
					];
					break;
				case 'custom':
					if ( ! ( $settings['2fa_bypass_roles'] ?? null ) ) {
						$bypass_group = [];
						break;
					}

					$bypass_group = [
						$upgrader->find_or_create( __( 'Passwordless Login Two-Factor Bypass', 'it-l10n-ithemes-security-pro' ), static function ( User_Group $user_group ) use ( $settings ) {
							$user_group->set_roles( ITSEC_Lib::sanitize_roles( $settings['2fa_bypass_roles'] ) );
						} )->get_id(),
					];
					break;
				case 'none':
				default:
					$bypass_group = [];
					break;
			}

			ITSEC_Modules::set_setting( 'passwordless-login', '2fa_bypass_group', $bypass_group );
		}
	}
}

new ITSEC_Passwordless_Login_Setup();
