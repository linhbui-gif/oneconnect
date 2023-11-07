<?php

namespace iThemesSecurity\Import_Export\Import;

use iThemesSecurity\Import_Export\Export\Export;

interface Transformation {

	/**
	 * Transforms an export based on the provided import context.
	 *
	 * @param Export         $export
	 * @param Import_Context $context
	 *
	 * @return Export
	 */
	public function transform( Export $export, Import_Context $context ): Export;

	/**
	 * Gets the locations, identified by dotted-paths, where a user is stored in the export data.
	 *
	 * @return array
	 */
	public function get_user_paths(): array;

	/**
	 * Gets the locations, identified by dotted-paths, where a role is stored in the export data.
	 *
	 * @return array
	 */
	public function get_role_paths(): array;
}
