<?php

namespace iThemesSecurity\WebAuthn\REST;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\PublicKeyCredential_Record;
use iThemesSecurity\WebAuthn\PublicKeyCredential_Record_Repository;
use iThemesSecurity\WebAuthn\PublicKeyCredentialUserEntity_Factory;

final class Credentials extends \WP_REST_Controller {

	private const PATTERN = '[\w\_\-]+';

	/** @var PublicKeyCredential_Record_Repository */
	private $repository;

	/** @var PublicKeyCredentialUserEntity_Factory */
	private $user_factory;

	public function __construct(
		PublicKeyCredential_Record_Repository $repository,
		PublicKeyCredentialUserEntity_Factory $user_factory
	) {
		$this->repository   = $repository;
		$this->user_factory = $user_factory;
		$this->namespace    = 'ithemes-security/v1';
		$this->rest_base    = 'webauthn/credentials';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, $this->rest_base, [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_collection_params(),
			],
			'schema' => [ $this, 'get_public_item_schema' ]
		] );
		register_rest_route( $this->namespace, sprintf( '/%s/(?P<id>%s)', $this->rest_base, self::PATTERN ), [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_item' ],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
				'args'                => [
					'context' => $this->get_context_param( [ 'default' => 'view' ] ),
				],
			],
			[
				'methods'             => 'PUT',
				'callback'            => [ $this, 'update_item' ],
				'permission_callback' => [ $this, 'update_item_permissions_check' ],
				'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
			],
			[
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				'args'                => [
					'force' => [
						'type'    => 'boolean',
						'default' => false,
					],
				],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );
	}

	public function get_items_permissions_check( $request ) {
		return is_user_logged_in();
	}

	public function get_items( $request ): \WP_REST_Response {
		$user = $this->user_factory->make( wp_get_current_user() );

		if ( ! $user->is_success() ) {
			return $user->as_rest_response();
		}

		$records = $this->repository->get_credentials_for_user( $user->get_data(), '' );

		if ( ! $records->is_success() ) {
			return $records->as_rest_response();
		}

		return new \WP_REST_Response( array_map( function ( PublicKeyCredential_Record $record ) use ( $request ) {
			return $this->prepare_response_for_collection( $this->prepare_item_for_response( $record, $request ) );
		}, $records->get_data() ) );
	}

	public function get_item_permissions_check( $request ) {
		$can_read = $this->check_read_permission( $request );

		if ( ! $can_read->is_success() ) {
			return $can_read->get_error();
		}

		return true;
	}

	public function get_item( $request ) {
		$found = $this->find_record( $request );

		if ( ! $found->is_success() ) {
			return $found->as_rest_response();
		}

		return $this->prepare_item_for_response( $found->get_data(), $request );
	}

	public function update_item_permissions_check( $request ) {
		return $this->get_item_permissions_check( $request );
	}

	public function update_item( $request ) {
		$prepared = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared ) ) {
			return $prepared;
		}

		$persisted = $this->repository->persist( $prepared );

		if ( ! $persisted->is_success() ) {
			return $persisted->as_rest_response();
		}

		$request['context'] = 'edit';

		return $this->prepare_item_for_response( $prepared, $request );
	}

	public function delete_item_permissions_check( $request ) {
		return $this->get_item_permissions_check( $request );
	}

	public function delete_item( $request ) {
		$found = $this->find_record( $request );

		if ( ! $found->is_success() ) {
			return $found->as_rest_response();
		}

		if ( $request['force'] ) {
			$deleted = $this->repository->delete( $found->get_data() );

			if ( ! $deleted->is_success() ) {
				return $deleted->as_rest_response();
			}

			return new \WP_REST_Response( null, \WP_Http::NO_CONTENT );
		}

		$record = $found->get_data();
		$record->trash();
		$persisted = $this->repository->persist( $record );

		if ( ! $persisted->is_success() ) {
			return $persisted->as_rest_response();
		}

		$request['context'] = 'edit';

		return $this->prepare_item_for_response( $record, $request );
	}

	protected function prepare_item_for_database( $request ) {
		$found = $this->find_record( $request );

		if ( ! $found->is_success() ) {
			return $found->get_error();
		}

		$record = $found->get_data();

		if ( $request['label'] ) {
			$record->set_label( $request['label'] );
		}

		if ( $request['status'] === PublicKeyCredential_Record::S_ACTIVE ) {
			$record->restore();
		}

		return $record;
	}

	/**
	 * Prepares a Record for response.
	 *
	 * @param PublicKeyCredential_Record $item
	 * @param \WP_REST_Request           $request
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = [
			'id'              => $item->get_id()->as_ascii_fast(),
			'type'            => $item->get_type(),
			'transports'      => $item->get_transports(),
			'signature_count' => $item->get_signature_count(),
			'backup_eligible' => $item->is_eligible_for_backups(),
			'backed_up'       => $item->is_backed_up(),
			'created_at'      => \ITSEC_Lib::to_rest_date( $item->get_created_at() ),
			'last_used'       => $item->get_last_used() ? \ITSEC_Lib::to_rest_date( $item->get_last_used() ) : null,
			'trashed_at'      => $item->get_trashed_at() ? \ITSEC_Lib::to_rest_date( $item->get_trashed_at() ) : null,
			'label'           => $item->get_label(),
			'status'          => $item->get_status(),
		];
		$data = $this->filter_response_by_context( $data, $request['context'] );

		$response = new \WP_REST_Response( $data );
		$response->add_link( 'self', rest_url( sprintf(
			'%s/%s/%s',
			$this->namespace,
			$this->rest_base,
			$item->get_id()->as_ascii_fast()
		) ) );

		return $response;
	}

	/**
	 * Finds the credential record for the request.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return Result<PublicKeyCredential_Record>
	 */
	protected function find_record( \WP_REST_Request $request ): Result {
		return $this->repository->find_by_id( BinaryString::from_ascii_fast( $request['id'] ) );
	}

	protected function check_read_permission( \WP_REST_Request $request ): Result {
		$record = $this->find_record( $request );

		if ( ! $record->is_success() ) {
			return $record;
		}

		$user = $this->user_factory->make( wp_get_current_user() );

		if ( ! $user->is_success() ) {
			return $user;
		}

		if ( ! $user->get_data()->get_id()->equals( $record->get_data()->get_user() ) ) {
			return Result::error( new \WP_Error(
				'itsec.webauthn.rest.credentials.cannot-view',
				__( 'Sorry, you are not allowed to view that Credential.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => rest_authorization_required_code() ]
			) );
		}

		return Result::success();
	}

	public function get_item_schema() {
		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'ithemes-security-webauthn-credential',
			'type'       => 'object',
			'properties' => [
				'id'              => [
					'type'     => 'string',
					'pattern'  => self::PATTERN,
					'readonly' => true,
					'context'  => [ 'view', 'edit', 'embed' ],
				],
				'type'            => [
					'type'     => 'string',
					'readonly' => true,
					'context'  => [ 'view', 'edit' ],
				],
				'transports'      => [
					'type'     => 'array',
					'items'    => [
						'type' => 'string',
					],
					'readonly' => true,
					'context'  => [ 'view', 'edit' ],
				],
				'signature_count' => [
					'type'     => 'integer',
					'minimum'  => 0,
					'readonly' => true,
					'context'  => [ 'view', 'edit' ],
				],
				'backup_eligible' => [
					'type'     => 'boolean',
					'readonly' => true,
					'context'  => [ 'view', 'edit', 'embed' ],
				],
				'backed_up'       => [
					'type'     => 'boolean',
					'readonly' => true,
					'context'  => [ 'view', 'edit', 'embed' ],
				],
				'created_at'      => [
					'type'     => 'string',
					'format'   => 'date-time',
					'readonly' => true,
					'context'  => [ 'view', 'edit', 'embed' ],
				],
				'last_used'       => [
					'type'     => [ 'string', 'null' ],
					'format'   => 'date-time',
					'readonly' => true,
					'context'  => [ 'view', 'edit', 'embed' ],
				],
				'trashed_at'      => [
					'type'     => [ 'string', 'null' ],
					'format'   => 'date-time',
					'readonly' => true,
					'context'  => [ 'view', 'edit', 'embed' ],
				],
				'label'           => [
					'type'      => 'string',
					'minLength' => 1,
					'context'   => [ 'view', 'edit', 'embed' ],
				],
				'status'          => [
					'type'    => 'string',
					'enum'    => [ PublicKeyCredential_Record::S_ACTIVE, PublicKeyCredential_Record::S_TRASH ],
					'context' => [ 'view', 'edit', 'embed' ],
				],
			],
		];
	}

	public function get_collection_params() {
		return [
			'context' => $this->get_context_param( [ 'default' => 'view' ] ),
		];
	}
}
