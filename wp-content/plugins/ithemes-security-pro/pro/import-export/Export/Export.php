<?php

namespace iThemesSecurity\Import_Export\Export;

final class Export implements \JsonSerializable {

	/** @var array */
	private $data;

	/**
	 * Creates a new empty export.
	 *
	 * @param string $id
	 */
	public function __construct( string $id ) {
		$this->data = [ 'id' => $id, 'metadata' => [], 'sources' => [] ];
	}

	/**
	 * Creates an export from a set of existing data.
	 *
	 * @param array $data
	 *
	 * @return static
	 */
	public static function from_data( array $data ): self {
		$export       = new self( '' );
		$export->data = $data;

		return $export;
	}

	/**
	 * Gets the UUID identifying this export.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->data['id'];
	}

	/**
	 * Attaches metadata to an export.
	 *
	 * @param string             $title
	 * @param int                $build
	 * @param string             $version
	 * @param \DateTimeInterface $exported_at
	 * @param \WP_User|null      $exported_by
	 * @param string             $home_url
	 * @param string             $abspath
	 *
	 * @return $this
	 */
	public function attach_metadata(
		string $title,
		int $build,
		string $version,
		\DateTimeInterface $exported_at,
		$exported_by,
		string $home_url,
		string $abspath
	): self {
		$this->data['metadata'] = [
			'title'       => $title,
			'build'       => $build,
			'version'     => $version,
			'exported_at' => $exported_at->format( \ITSEC_Lib_REST::DATE_FORMAT ),
			'exported_by' => self::format_user( $exported_by ),
			'home_url'    => $home_url,
			'abspath'     => $abspath,
		];

		return $this;
	}

	/**
	 * Gets the user provided title for the export.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return $this->get_metadata( 'title' );
	}

	/**
	 * Gets the iThemes Security build number the export was generated for.
	 *
	 * @return int
	 */
	public function get_build(): int {
		return $this->get_metadata( 'build' );
	}

	/**
	 * Gets the iThemes Security version string the export was generated for.
	 *
	 * @return string
	 */
	public function get_version(): string {
		return $this->get_metadata( 'version' );
	}

	/**
	 * Gets the time the export was generated at in UTC.
	 *
	 * @return \DateTimeInterface
	 */
	public function get_exported_at(): \DateTimeInterface {
		return new \DateTimeImmutable( $this->get_metadata( 'exported_at' ), new \DateTimeZone( 'UTC' ) );
	}

	/**
	 * Gets the user who generated the export.
	 *
	 * This may be null if there is no user with the associated email address,
	 * if you need to account for this scenario, get the raw exported_by data
	 * from {@see get_metadata()}.
	 *
	 * @return \WP_User|null
	 */
	public function get_exported_by() {
		return get_user_by( 'email', $this->get_metadata( 'user' )['email'] ) ?: null;
	}

	/**
	 * Gets the home URL for the export.
	 *
	 * @return string
	 */
	public function get_home_url(): string {
		return $this->get_metadata( 'home_url' );
	}

	/**
	 * Gets the ABSPATH from the exported site.
	 *
	 * @return string
	 */
	public function get_abspath(): string {
		return $this->get_metadata( 'abspath' );
	}

	/**
	 * Gets a specific metadata item from the export.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get_metadata( string $key ) {
		return $this->data['metadata'][ $key ];
	}

	/**
	 * Gets the list of sources included in this export.
	 *
	 * @return array
	 */
	public function get_sources(): array {
		return array_keys( $this->data['sources'] );
	}

	/**
	 * Sets a source's data for the export.
	 *
	 * @param string $slug
	 * @param mixed  $data
	 *
	 * @return $this
	 */
	public function set_data( string $slug, $data ): self {
		$this->data['sources'][ $slug ] = $data;

		return $this;
	}

	/**
	 * Creates a new Export object with the given data set.
	 *
	 * @param string $slug
	 * @param mixed  $data
	 *
	 * @return $this
	 */
	public function with_data( string $slug, $data ): self {
		$next = clone $this;

		$next->data['sources'][ $slug ] = $data;

		return $next;
	}

	/**
	 * Checks if the export has data from the given source.
	 *
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function has_data( string $slug ): bool {
		return isset( $this->data['sources'][ $slug ] );
	}

	/**
	 * Retrieves the source's data from the export.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */
	public function get_data( string $slug ) {
		return $this->data['sources'][ $slug ] ?? null;
	}

	/**
	 * Validates the export against a given JSON schema.
	 *
	 * @param array $schema
	 *
	 * @return true|\WP_Error
	 */
	public function validate_against( array $schema ) {
		return rest_validate_value_from_schema( $this->data, $schema );
	}

	public function jsonSerialize(): array {
		return $this->data;
	}

	/**
	 * Formats a user object for inclusion in the export.
	 *
	 * @param \WP_User|null $user
	 *
	 * @return array
	 */
	public static function format_user( \WP_User $user = null ): array {
		if ( ! $user || ! $user->exists() ) {
			return [];
		}

		return [
			'id'    => $user->ID,
			'email' => $user->user_email,
			'name'  => $user->display_name,
		];
	}

	/**
	 * Formats a role for inclusion in the export.
	 *
	 * @param string $role
	 *
	 * @return array
	 */
	public static function format_role( string $role ): array {
		if ( ! $role ) {
			return [];
		}

		return [
			'slug'  => $role,
			'label' => wp_roles()->role_names[ $role ] ?? '',
		];
	}
}
