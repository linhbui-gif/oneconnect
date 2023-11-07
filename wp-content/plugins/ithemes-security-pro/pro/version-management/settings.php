<?php

use iThemesSecurity\Config_Settings;

final class ITSEC_Version_Management_Settings extends Config_Settings {
	public function load() {
		parent::load();
		$this->settings['wordpress_automatic_updates'] = get_site_option( 'auto_update_core_major' ) === 'enabled';
	}

	protected function handle_settings_changes( $old_settings ) {
		parent::handle_settings_changes( $old_settings );

		if (
			$this->settings['wordpress_automatic_updates'] !== $old_settings['wordpress_automatic_updates']
		) {
			update_site_option( 'auto_update_core_major', $this->settings['wordpress_automatic_updates'] ? 'enabled' : 'unset' );
		}
	}
}

ITSEC_Modules::register_settings( new ITSEC_Version_Management_Settings( ITSEC_Modules::get_config( 'version-management' ) ) );
