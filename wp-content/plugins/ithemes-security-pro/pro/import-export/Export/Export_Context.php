<?php

namespace iThemesSecurity\Import_Export\Export;

final class Export_Context {

	/** @var array|true */
	private $sources;

	/** @var array */
	private $options;

	public function __construct( $sources = true, array $options = [] ) {
		if ( $sources !== true && ! is_array( $sources ) ) {
			throw new \InvalidArgumentException( '$sources must be either true or an array of source names.' );
		}

		$this->sources = $sources;
		$this->options = $options;
	}

	public function is_source_included( string $source ): bool {
		return $this->sources === true || in_array( $source, $this->sources, true );
	}

	/**
	 * Validates options against the schema.
	 *
	 * This also set's up default values in case any options are omitted.
	 *
	 * @param array $schema
	 *
	 * @return true|\WP_Error
	 */
	public function validate_options_against( array $schema ) {
		$valid = rest_validate_value_from_schema( $this->options, $schema );

		if ( is_wp_error( $valid ) ) {
			return $valid;
		}

		foreach ( $schema['properties'] as $property => $config ) {
			if (
				! array_key_exists( $property, $this->options ) &&
				array_key_exists( 'default', $config )
			) {
				$this->options[ $property ] = $config['default'];
			}
		}

		return true;
	}

	public function get_options( string $source ): array {
		return $this->options[ $source ] ?? [];
	}
}
