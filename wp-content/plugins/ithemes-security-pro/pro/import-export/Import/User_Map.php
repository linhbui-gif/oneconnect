<?php

namespace iThemesSecurity\Import_Export\Import;

final class User_Map {

	private $explicit_map = [];
	private $find_matches = true;

	/**
	 * Sets a map of export user ids to import user ids.
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
	 * Disables automatically looking for matches based on the user's email address.
	 *
	 * @return $this
	 */
	public function disable_finding_matches(): self {
		$this->find_matches = true;

		return $this;
	}

	/**
	 * Gets the user corresponding to the export's user definition.
	 *
	 * @param array $user
	 *
	 * @return int The mapped user id, or 0.
	 */
	public function get_mapped_user( array $user ): int {
		if ( ! $user ) {
			return 0;
		}

		if ( isset( $this->explicit_map[ $user['id'] ] ) ) {
			return $this->explicit_map[ $user['id'] ];
		}

		if ( $this->find_matches && $match = get_user_by( 'email', $user['email'] ) ) {
			return $match->ID;
		}

		return 0;
	}

}
