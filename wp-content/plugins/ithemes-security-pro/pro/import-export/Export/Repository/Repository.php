<?php

namespace iThemesSecurity\Import_Export\Export\Repository;

use iThemesSecurity\Exception\WP_Error;
use iThemesSecurity\Import_Export\Export\Export;

interface Repository {
	public function next_id(): string;

	public function all(): array;

	public function get( string $id ): Export;

	public function has( string $id ): bool;

	/**
	 * Persists an export to the repository.
	 *
	 * @param Export $export
	 *
	 * @return void
	 *
	 * @throws WP_Error
	 */
	public function persist( Export $export );

	/**
	 * Permanently deletes an export from the repository.
	 *
	 * @param Export $export
	 *
	 * @return void
	 *
	 * @throws WP_Error
	 */
	public function delete( Export $export );
}
