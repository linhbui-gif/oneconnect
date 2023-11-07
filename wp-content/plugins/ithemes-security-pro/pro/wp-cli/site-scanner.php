<?php

use iThemesSecurity\Site_Scanner\Repository\Repository;

class ITSEC_Site_Scanner_Command {

	/** @var Repository */
	private $repository;

	public function __construct( Repository $repository ) { $this->repository = $repository; }

	/**
	 * Perform a scan.
	 *
	 * ## OPTIONS
	 *
	 * [<site_id>]
	 * : Optionally, scan a site other than the main site in a multisite setup.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: description
	 * options:
	 *   - code
	 *   - description
	 *   - json
	 * ---
	 */
	public function scan( $args, $assoc_args ) {

		if ( isset( $args[0] ) ) {
			$site_id = (int) $args[0];
		} else {
			$site_id = 0;
		}

		if ( $site_id && ! is_multisite() ) {
			WP_CLI::error( 'Specifying a site ID is only supported on multisite.' );
		}

		ITSEC_Modules::load_module_file( 'api.php', 'site-scanner' );
		ITSEC_Modules::load_module_file( 'util.php', 'site-scanner' );
		$scan = ITSEC_Site_Scanner_API::scan( $site_id );

		if ( $scan->is_error() ) {
			WP_CLI::error( $scan->get_error() );
		}

		$code = $scan->get_code();

		switch ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'description' ) ) {
			case 'code':
				if ( 'clean' === $code ) {
					WP_CLI::success( $code );
				} else {
					WP_CLI::warning( $code );
				}
				break;
			case 'description':
				$description = ITSEC_Site_Scanner_Util::get_scan_code_description( $code );

				if ( 'clean' === $code ) {
					WP_CLI::success( $description );
				} else {
					WP_CLI::warning( $description );
				}
				break;
			case 'json':
				$json = [
					'id'      => $scan->get_id(),
					'code'    => $scan->get_code(),
					'errors'  => $scan->get_errors(),
					'entries' => [],
				];

				foreach ( $scan->get_entries() as $entry ) {
					$entry_data = [
						'slug'   => $entry->get_slug(),
						'title'  => $entry->get_title(),
						'status' => $entry->get_status(),
						'issues' => [],
					];

					foreach ( $entry->get_issues() as $issue ) {
						$entry_data['issues'][] = [
							'id'          => $issue->get_id(),
							'status'      => $entry->get_status(),
							'description' => $issue->get_description(),
							'link'        => $issue->get_link(),
							'meta'        => $issue->get_meta(),
						];
					}

					$json['entries'][] = $entry_data;
				}

				WP_CLI::line( json_encode( $json ) );
				break;
			default:
				WP_CLI::error( 'Invalid format.' );
		}
	}

	/**
	 * Notifies the configured users about a Scan result.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Scan id
	 */
	public function notify( $args ) {
		list( $id ) = $args;

		$scan = $this->repository->get_scan( (int) $id );

		if ( is_wp_error( $scan ) ) {
			WP_CLI::error( $scan );
		}

		$sent = ITSEC_Site_Scanner_Mail::send( $scan );

		if ( $sent ) {
			WP_CLI::success( 'Notified configured recipients about scan results.' );
		} else {
			WP_CLI::error( 'Failed to send notification.' );
		}
	}
}

WP_CLI::add_command( 'itsec site-scanner', new ITSEC_Site_Scanner_Command(
	ITSEC_Modules::get_container()->get( Repository::class )
) );
