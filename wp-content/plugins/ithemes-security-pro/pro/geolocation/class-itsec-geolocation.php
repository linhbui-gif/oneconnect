<?php

/**
 * Class ITSEC_Geolocation
 */
class ITSEC_Geolocation {

	/**
	 * Run the module.
	 */
	public function run() {
		add_filter( 'itsec_geolocator', array( $this, 'persistent_cache' ) );
		add_filter( 'itsec_geolocator_apis', array( $this, 'register_geolocator_apis' ) );
		add_filter( 'itsec_static_map_apis', array( $this, 'register_static_map_apis' ) );
		add_action( 'itsec_scheduled_geolocation-refresh', array( $this, 'on_refresh' ) );
	}

	/**
	 * Customize the main Geolocator instance.
	 *
	 * @param ITSEC_Geolocator $geolocator
	 *
	 * @return ITSEC_Geolocator
	 */
	public function persistent_cache( $geolocator ) {
		return new ITSEC_Geolocator_Cache( $geolocator );
	}

	/**
	 * Register geolocator APIs.
	 *
	 * @param array $apis
	 *
	 * @return array
	 */
	public function register_geolocator_apis( $apis ) {
		$has_mm = false;

		if ( ( $mm_api = new ITSEC_Geolocator_MaxMind_API() ) && $mm_api->is_available() ) {
			$has_mm = true;
			$apis[] = $mm_api;
		}

		if ( ( $mm_db = new ITSEC_Geolocator_MaxMind_DB() ) && $mm_db->is_available() ) {
			$has_mm = true;
			$apis[] = $mm_db;
		}

		if ( ! $has_mm ) {
			$apis[] = new ITSEC_Geolocator_IP_Info();
			$apis[] = new ITSEC_Geolocator_Geobytes();
			$apis[] = new ITSEC_Geolocator_GeoPlugin();
			$apis[] = new ITSEC_Geolocator_IPAPI();
		}

		return $apis;
	}

	/**
	 * Register the static map APIs.
	 *
	 * @param array $apis
	 *
	 * @return array
	 */
	public function register_static_map_apis( $apis ) {
		$apis[] = new ITSEC_Static_Map_API_MapQuest();
		$apis[] = new ITSEC_Static_Map_API_Mapbox();

		return $apis;
	}

	public function on_refresh( ITSEC_Job $job ) {
		if ( empty( ITSEC_Modules::get_setting( 'geolocation', 'maxmind_lite' )['key'] ) ) {
			return;
		}

		ITSEC_Geolocator_MaxMind_DB::download();
	}
}
