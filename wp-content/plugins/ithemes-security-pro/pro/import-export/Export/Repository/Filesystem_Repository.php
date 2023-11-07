<?php

namespace iThemesSecurity\Import_Export\Export\Repository;

use iThemesSecurity\Exception\WP_Error;
use iThemesSecurity\Import_Export\Export\Export;

final class Filesystem_Repository implements Repository {

	/** @var string */
	private $directory;

	public function __construct( string $directory ) { $this->directory = trailingslashit( $directory ); }

	public function next_id(): string {
		return \ITSEC_Lib::generate_uuid4();
	}

	public function all(): array {
		$files = \ITSEC_Lib_Directory::read( $this->directory );

		if ( is_wp_error( $files ) ) {
			return [];
		}

		$exports = [];

		foreach ( $files as $file ) {
			if ( ! \ITSEC_Lib::str_ends_with( $file, '.json' ) ) {
				continue;
			}

			try {
				$exports[] = $this->hydrate_file( $file );
			} catch ( WP_Error $e ) {
				continue;
			}
		}

		usort( $exports, static function ( Export $a, Export $b ) {
			return $b->get_exported_at()->getTimestamp() <=> $a->get_exported_at()->getTimestamp();
		} );

		return $exports;
	}

	public function get( string $id ): Export {
		$file = $this->directory . $id . '.json';

		if ( ! \ITSEC_Lib_File::exists( $file ) ) {
			throw new WP_Error( new \WP_Error(
				'itsec.import-export.export.filesystem-repository.not-found',
				__( 'The export file does not exist.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		return $this->hydrate_file( $file );
	}

	public function has( string $id ): bool {
		return \ITSEC_Lib_File::exists( $this->directory . $id . '.json' );
	}

	public function persist( Export $export ) {
		$written = \ITSEC_Lib_File::write(
			$this->directory . $export->get_id() . '.json',
			wp_json_encode( $export )
		);

		if ( is_wp_error( $written ) ) {
			throw new WP_Error( $written );
		}
	}

	public function delete( Export $export ) {
		$removed = \ITSEC_Lib_File::remove(
			$this->directory . $export->get_id() . '.json'
		);

		if ( is_wp_error( $removed ) ) {
			throw new WP_Error( $removed );
		}
	}

	private function hydrate_file( string $file ): Export {
		$json = \ITSEC_Lib_File::read( $file );

		if ( is_wp_error( $json ) ) {
			throw new WP_Error( $json );
		}

		if ( ! $json ) {
			throw new WP_Error( new \WP_Error(
				'itsec.import-export.export.filesystem-repository.empty-file',
				__( 'The export file is empty.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		if ( ! $data = json_decode( $json, true ) ) {
			throw new WP_Error( new \WP_Error(
				'itsec.import-export.export.filesystem-repository.invalid-json',
				__( 'The export file does not contain valid json.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		return Export::from_data( $data );
	}
}
