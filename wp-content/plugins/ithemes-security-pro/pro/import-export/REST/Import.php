<?php

namespace iThemesSecurity\Import_Export\REST;

use iThemesSecurity\Import_Export\Export\Export;
use iThemesSecurity\Import_Export\Export\Export_File_Manager;
use iThemesSecurity\Import_Export\Import\Import_Context;
use iThemesSecurity\Import_Export\Import\Importer;
use iThemesSecurity\Import_Export\Import\Role_Map;
use iThemesSecurity\Import_Export\Import\User_Map;
use iThemesSecurity\Lib\Result;

final class Import extends \WP_REST_Controller {

	/** @var Importer */
	private $importer;

	/** @var Export_File_Manager */
	private $file_manager;

	public function __construct(
		Importer $importer,
		Export_File_Manager $file_manager
	) {
		$this->importer     = $importer;
		$this->file_manager = $file_manager;

		$this->namespace = 'ithemes-security/rpc';
		$this->rest_base = 'import';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, $this->rest_base . '/validate', [
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'validate' ],
			'permission_callback' => 'ITSEC_Core::current_user_can_manage',
		] );
		register_rest_route( $this->namespace, $this->rest_base . '/transform', [
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'transform' ],
			'permission_callback' => 'ITSEC_Core::current_user_can_manage',
			'args'                => $this->get_import_params(),
		] );
		register_rest_route( $this->namespace, $this->rest_base . '/run', [
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'run' ],
			'permission_callback' => 'ITSEC_Core::current_user_can_manage',
			'args'                => $this->get_import_params(),
		] );
	}

	public function validate( \WP_REST_Request $request ): \WP_REST_Response {
		$export = $this->get_export_from_request( $request );

		if ( ! $export->is_success() ) {
			return $export->as_rest_response();
		}

		$context  = new Import_Context( [] );
		$imported = $this->importer->import( $export->get_data(), $context );

		if ( ! $imported->is_success() ) {
			return $imported->as_rest_response();
		}

		return $export->as_rest_response();
	}

	public function transform( \WP_REST_Request $request ): \WP_REST_Response {
		$export  = Export::from_data( $request['export'] );
		$context = $this->hydrate_context_from_request( $request );

		return new \WP_REST_Response( $this->importer->transform( $export, $context )->jsonSerialize() );
	}

	public function run( \WP_REST_Request $request ): \WP_REST_Response {
		$export   = Export::from_data( $request['export'] );
		$context  = $this->hydrate_context_from_request( $request );
		$imported = $this->importer->import( $export, $context );

		return $imported->as_rest_response();
	}

	private function hydrate_context_from_request( \WP_REST_Request $request ): Import_Context {
		$user_map = new User_Map();

		if ( $request['user_map'] !== true ) {
			$user_map->disable_finding_matches();
			$user_map->set_explicit_map( $request['user_map'] );
		}

		$role_map = new Role_Map();

		if ( $request['role_map'] !== true ) {
			$role_map->disable_finding_matches();
			$role_map->set_explicit_map( $request['role_map'] );
		}

		return new Import_Context( $request['sources'], $user_map, $role_map );
	}

	/**
	 * Gets the Export from a request object.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return Result<Export>
	 */
	private function get_export_from_request( \WP_REST_Request $request ): Result {
		if ( $request->is_json_content_type() ) {
			return Result::success( Export::from_data( $request->get_json_params() ) );
		}

		$file = $request->get_file_params()['file'] ?? [];

		if ( empty( $file['tmp_name'] ) ) {
			return Result::error( new \WP_Error(
				'itsec_import_missing_export',
				__( 'Missing export file.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::BAD_REQUEST ]
			) );
		}

		return $this->file_manager->read_file( $file['tmp_name'] );
	}

	private function get_import_params(): array {
		return [
			'export'   => [
				'type'     => 'object',
				'required' => true,
			],
			'sources'  => [
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
			'user_map' => [
				'type'                 => [ 'object', 'boolean' ],
				'patternProperties'    => [
					'\d+' => [
						'type' => 'integer',
					],
				],
				'additionalProperties' => false,
				'default'              => true,
			],
			'role_map' => [
				'type'                 => [ 'object', 'boolean' ],
				'additionalProperties' => [
					'type' => 'string',
				],
				'default'              => true,
			],
		];
	}
}
