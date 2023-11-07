<?php

use iThemesSecurity\Import_Export\Export\Export;
use iThemesSecurity\Import_Export\Export\Export_Context;
use iThemesSecurity\Import_Export\Export\Exporter;
use iThemesSecurity\Import_Export\Export\Export_File_Manager;
use iThemesSecurity\Import_Export\Export\Repository\Repository;
use iThemesSecurity\Import_Export\Import\Import_Context;
use iThemesSecurity\Import_Export\Import\Importer;
use function WP_CLI\Utils\get_flag_value;

/**
 * Perform imports.
 */
class ITSEC_Import_Export_Import_Command {

	/** @var Repository */
	private $repository;

	/** @var Importer */
	private $importer;

	/** @var Export_File_Manager */
	private $file_manager;

	public function __construct( Repository $repository, Importer $importer, Export_File_Manager $file_manager ) {
		$this->repository   = $repository;
		$this->importer     = $importer;
		$this->file_manager = $file_manager;
	}

	/**
	 * Import settings from a settings export.
	 *
	 * ## OPTIONS
	 *
	 * <export>
	 * : Export id or path to the export JSON or Zip file.
	 *
	 * [--sources=<sources>]
	 * : Optionally, limit the export sources to import.
	 */
	public function __invoke( $args, $assoc_args ) {
		list( $export ) = $args;
		$sources = get_flag_value( $assoc_args, 'sources', true );
		$sources = is_string( $sources ) ? explode( ',', $sources ) : true;

		if ( wp_is_uuid( $export, 4 ) ) {
			if ( ! $this->repository->has( $export ) ) {
				WP_CLI::error( 'Export not found.' );
			}

			$export = $this->repository->get( $export );
		} else {
			if ( ! file_exists( $export ) ) {
				WP_CLI::error( 'Invalid export file. File does not exists.' );
			}

			$read = $this->file_manager->read_file( $export );
			$read->for_wp_cli();
			$export = $read->get_data();
		}

		$context = new Import_Context( $sources );
		$result  = $this->importer->import( $export, $context );

		$result->for_wp_cli();
		WP_CLI::success( 'Import complete.' );
	}
}

/**
 * Manage and create exports.
 */
class ITSEC_Import_Export_Export_Command {

	const DEFAULT_COLUMNS = [
		'id',
		'title',
		'exported_at',
		'sources',
	];

	const ALL_COLUMNS = [
		'id',
		'title',
		'exported_at',
		'exported_by',
		'version',
		'sources',
	];

	/** @var Repository */
	private $repository;

	/** @var Exporter */
	private $exporter;

	public function __construct( Repository $repository, Exporter $exporter ) {
		$this->repository = $repository;
		$this->exporter   = $exporter;
	}

	/**
	 * Creates a new export.
	 *
	 * ## OPTIONS
	 *
	 * [--title=<title>]
	 * : Provide a named title for the export.
	 *
	 * [--sources=<sources>]
	 * : Optionally, limit the export sources.
	 *
	 * [--options=<options>]
	 * : Options to customize the export. Passed as JSON.
	 *
	 * [--save]
	 * : Optionally, save the export to the site.
	 *
	 * [--notify=<notify>]
	 * : Send a notification to the given email address with the export file.
	 *
	 * [--porcelain]
	 * : Only output the export data, if saving, only outputs the export id.
	 *
	 * [--pretty-print]
	 * : Optionally, pretty-print the export content.
	 */
	public function create( $args, $assoc_args ) {
		$title        = get_flag_value( $assoc_args, 'title', '' );
		$sources      = get_flag_value( $assoc_args, 'sources', true );
		$sources      = is_string( $sources ) ? explode( ',', $sources ) : true;
		$options      = get_flag_value( $assoc_args, 'options', '[]' );
		$options      = json_decode( $options, true );
		$save         = get_flag_value( $assoc_args, 'save', false );
		$notify       = get_flag_value( $assoc_args, 'notify', false );
		$porcelain    = get_flag_value( $assoc_args, 'porcelain', false );
		$pretty_print = get_flag_value( $assoc_args, 'pretty-print', false );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			WP_CLI::error( 'Invalid JSON passed for --options flag: ' . json_last_error_msg() );
		}

		$context = new Export_Context( $sources, $options );
		$result  = $this->exporter->export( $context, wp_get_current_user(), $title );

		$result->for_wp_cli();

		/** @var Export $export */
		$export = $result->get_data();

		if ( $notify ) {
			$sent = $this->exporter->notify( $export, $notify );

			if ( ! $porcelain ) {
				if ( $sent ) {
					WP_CLI::success( 'Emailed export.' );
				} else {
					WP_CLI::warning( 'Failed to send notification.' );
				}
			}
		}

		if ( $save ) {
			try {
				$this->repository->persist( $export );

				if ( $porcelain ) {
					WP_CLI::log( $export->get_id() );
				} else {
					WP_CLI::success( sprintf( 'Saved export %s.', $export->get_id() ) );
				}
			} catch ( \iThemesSecurity\Exception\WP_Error $e ) {
				WP_CLI::error( $e->get_error() );
			}
		} else {
			WP_CLI::log( wp_json_encode( $export, $pretty_print ? JSON_PRETTY_PRINT : 0 ) );
		}
	}

	/**
	 * Lists the available exports on the site.
	 *
	 * ## OPTIONS
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each log item.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - ids
	 *   - json
	 *   - count
	 *   - yaml
	 * ---
	 *
	 * @subcommand list
	 */
	public function list_( $args, $assoc_args ) {
		$fields = get_flag_value( $assoc_args, 'fields', self::DEFAULT_COLUMNS );
		$format = get_flag_value( $assoc_args, 'format', 'table' );
		$field  = get_flag_value( $assoc_args, 'field' );

		if ( '*' === $fields ) {
			$fields = self::ALL_COLUMNS;
		}

		$format_args = [
			'format' => $format,
			'fields' => $fields,
			'field'  => $field,
		];
		$formatter   = new \WP_CLI\Formatter( $format_args );
		$formatter->display_items( array_map(
			function ( $export ) use ( $format ) { return $this->format_item( $export, $format ); },
			$this->repository->all()
		) );
	}

	/**
	 * Gets an export.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The export id.
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each log item.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - ids
	 *   - json
	 *   - count
	 *   - yaml
	 * ---
	 */
	public function get( $args, $assoc_args ) {
		list( $id ) = $args;

		if ( ! $this->repository->has( $id ) ) {
			WP_CLI::error( 'Export not found.' );
		}

		$fields = get_flag_value( $assoc_args, 'fields', self::DEFAULT_COLUMNS );
		$format = get_flag_value( $assoc_args, 'format', 'table' );
		$field  = get_flag_value( $assoc_args, 'field' );

		if ( '*' === $fields ) {
			$fields = self::ALL_COLUMNS;
		}

		$format_args = [
			'format' => $format,
			'fields' => $fields,
			'field'  => $field,
		];
		$formatter   = new \WP_CLI\Formatter( $format_args );
		$formatter->display_item( $this->format_item(
			$this->repository->get( $id ),
			$format
		) );
	}

	/**
	 * Show's an export content.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The export id.
	 *
	 * [--pretty-print]
	 * : Optionally, pretty-print the export content.
	 */
	public function show( $args, $assoc_args ) {
		list( $id ) = $args;

		$pretty_print = get_flag_value( $assoc_args, 'pretty-print', false );

		if ( ! $this->repository->has( $id ) ) {
			WP_CLI::error( 'Export not found.' );
		}

		$export = $this->repository->get( $id );
		WP_CLI::log( wp_json_encode( $export, $pretty_print ? JSON_PRETTY_PRINT : 0 ) );
	}

	/**
	 * Deletes an export.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The export id.
	 */
	public function delete( $args ) {
		list( $id ) = $args;

		if ( ! $this->repository->has( $id ) ) {
			WP_CLI::error( 'Export not found.' );
		}

		try {
			$this->repository->delete( $this->repository->get( $id ) );

			WP_CLI::success( 'Deleted export.' );
		} catch ( \iThemesSecurity\Exception\WP_Error $e ) {
			WP_CLI::error( $e->get_error() );
		}
	}

	/**
	 * Sends an export file via email.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The export id.
	 *
	 * <email>
	 * : The email address to send to.
	 */
	public function notify( $args ) {
		list( $id, $email ) = $args;

		if ( ! $this->repository->has( $id ) ) {
			WP_CLI::error( 'Export not found.' );
		}

		$sent = $this->exporter->notify(
			$this->repository->get( $id ),
			$email
		);

		if ( $sent ) {
			WP_CLI::success( 'Sent export notification.' );
		} else {
			WP_CLI::error( 'Failed to send export notification.' );
		}
	}

	private function format_item( Export $export, string $format ): array {
		$as_string = in_array( $format, [ 'table', 'csv' ], true );

		return [
			'id'          => $export->get_id(),
			'title'       => $export->get_title(),
			'exported_at' => $export->get_exported_at()->format( 'Y-m-d H:i:s' ),
			'exported_by' => $export->get_metadata( 'exported_by' )['id'] ?? 0,
			'version'     => $export->get_version(),
			'sources'     => $as_string ? implode( ',', $export->get_sources() ) : $export->get_sources(),
		];
	}
}

WP_CLI::add_command( 'itsec import-export import', new ITSEC_Import_Export_Import_Command(
	ITSEC_Modules::get_container()->get( Repository::class ),
	ITSEC_Modules::get_container()->get( Importer::class ),
	ITSEC_Modules::get_container()->get( Export_File_Manager::class )
) );
WP_CLI::add_command( 'itsec import-export export', new ITSEC_Import_Export_Export_Command(
	ITSEC_Modules::get_container()->get( Repository::class ),
	ITSEC_Modules::get_container()->get( Exporter::class )
) );
