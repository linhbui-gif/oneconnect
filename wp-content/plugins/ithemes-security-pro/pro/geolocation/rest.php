<?php

register_rest_route( 'ithemes-security/rpc', 'geolocation/maxmind-db-check', [
	'callback'            => function () {
		require_once( __DIR__ . '/geolocators/class-itsec-geolocator-maxmind-db.php' );

		return [
			'path'      => ITSEC_Geolocator_MaxMind_DB::get_db_path(),
			'available' => ( new ITSEC_Geolocator_MaxMind_DB() )->is_available(),
		];
	},
	'permission_callback' => 'ITSEC_Core::current_user_can_manage',
] );

register_rest_route( 'ithemes-security/rpc', 'geolocation/maxmind-db-download', [
	'methods'             => WP_REST_Server::CREATABLE,
	'args'                => [
		'api_key' => [
			'type'      => 'string',
			'minLength' => 1,
			'required'  => true,
		],
	],
	'callback'            => function ( $request ) {
		require_once( __DIR__ . '/geolocators/class-itsec-geolocator-maxmind-db.php' );

		$downloaded = ITSEC_Geolocator_MaxMind_DB::download( $request['api_key'] );

		if ( is_wp_error( $downloaded ) ) {
			return $downloaded;
		}

		return [
			'path' => ITSEC_Geolocator_MaxMind_DB::get_db_path(),
		];
	},
	'permission_callback' => 'ITSEC_Core::current_user_can_manage',
] );
