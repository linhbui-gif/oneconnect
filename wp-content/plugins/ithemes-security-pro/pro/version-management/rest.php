<?php

register_rest_route( 'ithemes-security/rpc', '/version-management/packages', [
	'callback' => function () {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$packages = [];

		foreach ( get_plugins() as $file => $plugin ) {
			$packages[] = [
				'id'   => "plugin:{$file}",
				'name' => $plugin['Name'],
				'file' => $file,
				'kind' => 'plugin',
			];
		}

		foreach ( wp_get_themes() as $file => $theme ) {
			$packages[] = [
				'id'   => "theme:{$file}",
				'name' => $theme->get( 'Name' ),
				'file' => $file,
				'kind' => 'theme',
			];
		}

		return $packages;
	},

	'permission_callback' => 'ITSEC_Core::current_user_can_manage',
] );
