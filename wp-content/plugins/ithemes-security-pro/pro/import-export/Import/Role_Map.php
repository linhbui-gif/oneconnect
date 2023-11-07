<?php

namespace iThemesSecurity\Import_Export\Import;

final class Role_Map {
	private $explicit_map = [];
	private $find_matches = true;

	/**
	 * Sets a map of export roles to import roles.
	 *
	 * @param array $map
	 *
	 * @return $this
	 */
	public function set_explicit_map( array $map ): self {
		$this->explicit_map = $map;

		return $this;
	}

	/**
	 * Disables automatically using the export role if it exists on the site.
	 *
	 * @return $this
	 */
	public function disable_finding_matches(): self {
		$this->find_matches = true;

		return $this;
	}

	public function get_mapped_role( string $role ): string {
		if ( isset( $this->explicit_map[ $role ] ) ) {
			return $this->explicit_map[ $role ];
		}

		if ( $this->find_matches && get_role( $role ) ) {
			return $role;
		}

		return '';
	}
}
