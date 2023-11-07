<?php

namespace iThemesSecurity\Import_Export\REST;

use iThemesSecurity\Contracts\Import_Export_Source;

class Sources extends \WP_REST_Controller {

	/** @var Import_Export_Source[] */
	private $sources;

	public function __construct( array $sources ) {
		$this->namespace = 'ithemes-security/v1';
		$this->rest_base = 'import-export/sources';
		$this->sources   = $sources;
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			'callback'            => [ $this, 'get_items' ],
			'permission_callback' => [ $this, 'get_items_permissions_check' ],
			'args'                => $this->get_collection_params(),
			'schema'              => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<slug>[\w\-]+)', [
			'callback'            => [ $this, 'get_item' ],
			'permission_callback' => [ $this, 'get_item_permissions_check' ],
			'args'                => [ 'context' => $this->get_context_param( [ 'default' => 'view' ] ) ],
			'schema'              => [ $this, 'get_public_item_schema' ],
		] );
	}

	public function get_items_permissions_check( $request ) {
		return \ITSEC_Core::current_user_can_manage();
	}

	public function get_items( $request ) {
		$response = [];

		foreach ( $this->sources as $source ) {
			$response[] = $this->prepare_response_for_collection(
				$this->prepare_item_for_response( $source, $request )
			);
		}

		return rest_ensure_response( $response );
	}

	public function get_item_permissions_check( $request ) {
		return \ITSEC_Core::current_user_can_manage();
	}

	public function get_item( $request ) {
		foreach ( $this->sources as $source ) {
			if ( $source->get_export_slug() === $request['slug'] ) {
				return $this->prepare_item_for_response( $source, $request );
			}
		}

		return new \WP_Error(
			'itsec_import_export_source_not_found',
			__( 'No import export source found with that id.', 'it-l10n-ithemes-security-pro' ),
			[ 'status' => \WP_Http::NOT_FOUND ]
		);
	}

	/**
	 * Prepares an import-export source for the REST API response.
	 *
	 * @param Import_Export_Source $item
	 * @param \WP_REST_Request     $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = [
			'slug'          => $item->get_export_slug(),
			'title'         => $item->get_export_title(),
			'description'   => $item->get_export_description(),
			'options'       => $item->get_export_options_schema() ?: null,
			'schema'        => $item->get_export_schema() ?: null,
			'transform_map' => [ 'users' => [], 'roles' => [] ],
		];

		foreach ( $item->get_transformations() as $transformation ) {
			$data['transform_map']['users'] = array_merge(
				$data['transform_map']['users'],
				$transformation->get_user_paths()
			);
			$data['transform_map']['roles'] = array_merge(
				$data['transform_map']['roles'],
				$transformation->get_role_paths()
			);
		}

		$data     = $this->filter_response_by_context( $data, $request['context'] );
		$response = new \WP_REST_Response( $data );
		$response->add_link( 'self', rest_url( sprintf(
			'%s/%s/%s',
			$this->namespace,
			$this->rest_base,
			$item->get_export_slug()
		) ) );

		return $response;
	}

	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->schema;
		}

		$this->schema = [
			'type'       => 'object',
			'properties' => [
				'slug'        => [
					'type'    => 'string',
					'context' => [ 'view', 'edit', 'embed' ],
				],
				'title'       => [
					'type'    => 'string',
					'context' => [ 'view', 'edit', 'embed' ],
				],
				'description' => [
					'type'    => 'string',
					'context' => [ 'view', 'edit', 'embed' ],
				],
				'options'     => [
					'type'    => [ 'object', 'null' ],
					'context' => [ 'view', 'edit' ],
				],
				'schema'      => [
					'type'    => [ 'object', 'null' ],
					'context' => [ 'edit' ],
				],
			]
		];

		return $this->schema;
	}

	public function get_collection_params() {
		return [
			'context' => $this->get_context_param( [ 'default' => 'view' ] ),
		];
	}
}
