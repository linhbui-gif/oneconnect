<?php

namespace iThemesSecurity\Import_Export\REST;

use iThemesSecurity\Exception\WP_Error;
use iThemesSecurity\Import_Export\Export\Export;
use iThemesSecurity\Import_Export\Export\Export_Context;
use iThemesSecurity\Import_Export\Export\Exporter;
use iThemesSecurity\Import_Export\Export\Repository\Repository;

class Exports extends \WP_REST_Controller {

	/** @var Repository */
	private $repository;

	/** @var Exporter */
	private $exporter;

	public function __construct( Repository $repository, Exporter $exporter, array $schema ) {
		$this->namespace  = 'ithemes-security/v1';
		$this->rest_base  = 'import-export/exports';
		$this->repository = $repository;
		$this->exporter   = $exporter;
		$this->schema     = $schema;

		foreach ( array_keys( $this->schema['properties'] ) as $property ) {
			$this->schema['properties'][ $property ]['context'] = 'sources' === $property ?
				[ 'view', 'edit' ] :
				[ 'view', 'edit', 'embed' ];
		}
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_collection_params(),
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_item' ],
				'permission_callback' => [ $this, 'create_item_permissions_check' ],
				'args'                => [
					'sources' => [
						'oneOf'   => [
							[
								'type' => 'boolean',
								'enum' => [ true ],
							],
							[
								'type'        => 'array',
								'items'       => [
									'type' => 'string',
								],
								'uniqueItems' => true,
							],
						],
						'default' => true,
					],
					'options' => [
						'type'    => 'object',
						'default' => [],
					],
					'title'   => [
						'type'    => 'string',
						'default' => '',
					],
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\w\-]+)', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_item' ],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
				'args'                => [
					'context' => $this->get_context_param( [ 'default' => 'view' ] ),
				],
			],
			[
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				'allow_batch'         => [ 'v1' => true ],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	public function get_items_permissions_check( $request ) {
		return \ITSEC_Core::current_user_can_manage();
	}

	public function get_items( $request ) {
		$response = [];

		foreach ( $this->repository->all() as $export ) {
			$response[] = $this->prepare_response_for_collection(
				$this->prepare_item_for_response( $export, $request )
			);
		}

		return rest_ensure_response( $response );
	}

	public function get_item_permissions_check( $request ) {
		return \ITSEC_Core::current_user_can_manage();
	}

	public function get_item( $request ) {
		if ( ! $this->repository->has( $request['id'] ) ) {
			return new \WP_Error(
				'itsec_export_not_found',
				__( 'No export found with that id.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::NOT_FOUND ]
			);
		}

		return $this->prepare_item_for_response( $this->repository->get( $request['id'] ), $request );
	}

	public function create_item_permissions_check( $request ) {
		return \ITSEC_Core::current_user_can_manage();
	}

	public function create_item( $request ) {
		$context = new Export_Context( $request['sources'], $request['options'] );
		$result  = $this->exporter->export(
			$context,
			wp_get_current_user() ?: null,
			$request['title']
		);

		if ( ! $result->is_success() ) {
			return $result->as_rest_response();
		}

		if ( $request['title'] ) {
			try {
				$this->repository->persist( $result->get_data() );
			} catch ( WP_Error $e ) {
				return $e->get_error();
			}
		}

		$request['context'] = 'edit';

		$response = $this->prepare_item_for_response( $result->get_data(), $request );
		$response->set_headers( $result->as_rest_response()->get_headers() );
		$response->header( 'Location', rest_url( sprintf(
			'%s/%s/%s',
			$this->namespace,
			$this->rest_base,
			$response->get_data()['id']
		) ) );
		$response->set_status( \WP_Http::CREATED );

		return $response;
	}

	public function delete_item_permissions_check( $request ) {
		return \ITSEC_Core::current_user_can_manage();
	}

	public function delete_item( $request ) {
		if ( ! $this->repository->has( $request['id'] ) ) {
			return new \WP_Error(
				'itsec_export_not_found',
				__( 'No export found with that id.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::NOT_FOUND ]
			);
		}

		$export = $this->repository->get( $request['id'] );

		try {
			$this->repository->delete( $export );
		} catch ( WP_Error $e ) {
			return $e->get_error();
		}

		return new \WP_REST_Response( null, \WP_Http::NO_CONTENT );
	}

	/**
	 * Prepares an export for the response.
	 *
	 * @param Export           $item
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = $item->jsonSerialize();
		$data = $this->filter_response_by_context( $data, $request['context'] );

		$response = new \WP_REST_Response( $data );
		$response->add_link( 'self', rest_url( sprintf( '%s/%s/%s', $this->namespace, $this->rest_base, $item->get_id() ) ) );

		return $response;
	}

	public function get_collection_params() {
		return [
			'context' => $this->get_context_param( [ 'default' => 'view' ] ),
		];
	}
}
