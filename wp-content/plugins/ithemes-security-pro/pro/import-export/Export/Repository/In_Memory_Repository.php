<?php

namespace iThemesSecurity\Import_Export\Export\Repository;

use iThemesSecurity\Exception\WP_Error;
use iThemesSecurity\Import_Export\Export\Export;

final class In_Memory_Repository implements Repository {

	private $memory = [];

	public function next_id(): string {
		return wp_generate_uuid4();
	}

	public function all(): array {
		return $this->memory;
	}

	public function get( string $id ): Export {
		if ( ! isset( $this->memory[ $id ] ) ) {
			throw new WP_Error( new \WP_Error(
				'itsec.import-export.export.in-memory-repository.not-found',
				__( 'The export does not exist.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		return $this->memory[ $id ];
	}

	public function has( string $id ): bool {
		return isset( $this->memory[ $id ] );
	}

	public function persist( Export $export ) {
		$this->memory[ $export->get_id() ] = $export;
	}

	public function delete( Export $export ) {
		unset( $this->memory[ $export->get_id() ] );
	}
}
