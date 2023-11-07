<?php

namespace iThemesSecurity\Import_Export;

use iThemesSecurity\Contracts\Import_Export_Source;
use iThemesSecurity\Strauss\Pimple\Container;

return static function ( Container $c ) {
	\ITSEC_Lib::extend_if_able( $c, 'import-export.sources', function ( $sources, $c ) {
		$sources[] = $c[ Sources\Settings::class ];

		return $sources;
	} );

	$c[ Sources\Settings::class ] = static function () {
		return new Sources\Settings();
	};

	$c[ Export\Repository\Repository::class ] = static function () {
		return new Export\Repository\Filesystem_Repository(
			\ITSEC_Core::get_storage_dir( 'exports' )
		);
	};

	$c[ Export\Export_File_Manager::class ] = static function () {
		return new Export\Export_File_Manager();
	};

	$c[ Export\Exporter::class ] = static function ( Container $c ) {
		return new Export\Exporter(
			$c[ Export\Repository\Repository::class ],
			$c[ Export\Export_File_Manager::class ],
			$c['import-export.options-schema'],
			$c['import-export.sources']
		);
	};

	$c[ Import\Importer::class ] = static function ( Container $c ) {
		return new Import\Importer(
			$c['import-export.export-schema'],
			$c['import-export.sources']
		);
	};

	$c[ REST\Exports::class ] = static function ( Container $c ) {
		return new REST\Exports(
			$c[ Export\Repository\Repository::class ],
			$c[ Export\Exporter::class ],
			$c['import-export.export-schema']
		);
	};

	$c[ REST\Import::class ] = static function ( Container $c ) {
		return new REST\Import(
			$c[ Import\Importer::class ],
			$c[ Export\Export_File_Manager::class ]
		);
	};

	$c[ REST\Sources::class ] = static function ( Container $c ) {
		return new REST\Sources( $c['import-export.sources'] );
	};

	$c['import-export.export-schema'] = static function ( Container $c ) {
		$base = \ITSEC_Lib_File::read( __DIR__ . '/Export/schema.json' );

		if ( ! $base || is_wp_error( $base ) ) {
			return [];
		}

		$schema = json_decode( $base, true );

		/** @var Import_Export_Source $source */
		foreach ( $c['import-export.sources'] as $source ) {
			$schema['properties'][ $source->get_export_slug() ] = $source->get_export_schema();
		}

		return \ITSEC_Lib::resolve_schema_refs( $schema );
	};

	$c['import-export.options-schema'] = static function ( Container $c ) {
		$schema = [
			'type'       => 'object',
			'properties' => [],
		];

		/** @var Import_Export_Source $source */
		foreach ( $c['import-export.sources'] as $source ) {
			$schema['properties'][ $source->get_export_slug() ] = $source->get_export_options_schema();
		}

		return \ITSEC_Lib::resolve_schema_refs( $schema );
	};

	\ITSEC_Lib::extend_if_able( $c, 'rest.controllers', function ( $sources, $c ) {
		$sources[] = $c[ REST\Exports::class ];
		$sources[] = $c[ REST\Sources::class ];
		$sources[] = $c[ REST\Import::class ];

		return $sources;
	} );
};
