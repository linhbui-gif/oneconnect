<?php

namespace iThemesSecurity\Import_Export\Import;

use iThemesSecurity\Import_Export\Export\Export;

final class Import_Context {

	/** @var array|true */
	private $sources;

	/** @var User_Map */
	private $user_map;

	/** @var Role_Map */
	private $role_map;

	public function __construct( $sources = true, User_Map $user_map = null, Role_Map $role_map = null ) {
		if ( $sources !== true && ! is_array( $sources ) ) {
			throw new \InvalidArgumentException( '$sources must be either true or an array of source names.' );
		}

		$this->sources  = $sources;
		$this->user_map = $user_map ?? new User_Map();
		$this->role_map = $role_map ?? new Role_Map();
	}

	/**
	 * Checks if the given import export source should be included in this import.
	 *
	 * @param string $source
	 *
	 * @return bool
	 */
	public function is_source_included( string $source ): bool {
		return $this->sources === true || in_array( $source, $this->sources, true );
	}

	/**
	 * Get the new user created for the existing user id.
	 *
	 * @param array $user
	 *
	 * @return \WP_User|null
	 */
	public function get_mapped_user( array $user ) {
		if ( $mapped = $this->user_map->get_mapped_user( $user ) ) {
			return get_userdata( $mapped ) ?: null;
		}

		return null;
	}

	/**
	 * Maps a list of users.
	 *
	 * @param array $users
	 *
	 * @return array
	 */
	public function map_user_list( array $users ): array {
		return array_reduce( $users, function ( $users, $user ) {
			if ( $mapped = $this->get_mapped_user( $user ) ) {
				$users[] = Export::format_user( $mapped );
			}

			return $users;
		}, [] );
	}

	/**
	 * Gets the new role corresponding to the export's role.
	 *
	 * @param string $role
	 *
	 * @return string
	 */
	public function get_mapped_role( string $role ): string {
		return $this->role_map->get_mapped_role( $role );
	}

	/**
	 * Maps a list of roles.
	 *
	 * @param array $roles
	 *
	 * @return array
	 */
	public function map_role_list( array $roles ): array {
		return array_reduce( $roles, function ( $roles, $role ) {
			if ( $mapped = $this->get_mapped_role( $role['slug'] ) ) {
				$roles[] = Export::format_role( $mapped );
			}

			return $roles;
		}, [] );
	}
}
