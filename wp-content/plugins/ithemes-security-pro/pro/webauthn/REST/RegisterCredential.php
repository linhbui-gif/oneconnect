<?php

namespace iThemesSecurity\WebAuthn\REST;

use iThemesSecurity\WebAuthn\DTO\AuthenticatorAttachment;
use iThemesSecurity\WebAuthn\DTO\AuthenticatorSelectionCriteria;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredential;
use iThemesSecurity\WebAuthn\DTO\ResidentKeyRequirement;
use iThemesSecurity\WebAuthn\DTO\UserVerificationRequirement;
use iThemesSecurity\WebAuthn\PublicKeyCredentialCreationOptions_Factory;
use iThemesSecurity\WebAuthn\RegistrationCeremony;
use iThemesSecurity\WebAuthn\Session_Storage;

final class RegisterCredential extends \WP_REST_Controller {

	/** @var RegistrationCeremony */
	private $ceremony;

	/** @var PublicKeyCredentialCreationOptions_Factory */
	private $options_factory;

	/** @var Session_Storage */
	private $session_storage;

	public function __construct(
		RegistrationCeremony $ceremony,
		PublicKeyCredentialCreationOptions_Factory $options_factory,
		Session_Storage $session_storage
	) {
		$this->namespace       = 'ithemes-security/rpc';
		$this->rest_base       = 'webauthn/register-credential';
		$this->ceremony        = $ceremony;
		$this->options_factory = $options_factory;
		$this->session_storage = $session_storage;
	}

	public function register_routes() {
		register_rest_route( $this->namespace, sprintf( '/%s', $this->rest_base ), [
			'methods'             => 'POST',
			'callback'            => [ $this, 'start_callback' ],
			'permission_callback' => 'is_user_logged_in',
			'args'                => [
				'authenticatorSelection' => [
					'type'       => 'object',
					'properties' => [
						'authenticatorAttachment' => [
							'type' => 'string',
							'enum' => AuthenticatorAttachment::ALL,
						],
						'residentKey'             => [
							'type' => 'string',
							'enum' => ResidentKeyRequirement::ALL,
						],
						'userVerification'        => [
							'type' => 'string',
							'enum' => UserVerificationRequirement::ALL,
						],
					]
				]
			],
		] );
		register_rest_route( $this->namespace, sprintf( '/%s/(?P<token>[\w\-]+)/create', $this->rest_base ), [
			'methods'             => 'POST',
			'callback'            => [ $this, 'register_callback' ],
			'permission_callback' => 'is_user_logged_in',
			'args'                => [
				'token'      => [
					'type'      => 'string',
					'minLength' => 1,
				],
				'label'      => [
					'type'      => 'string',
					'minLength' => 1,
				],
				'credential' => [
					'required'   => true,
					'type'       => 'object',
					'properties' => [
						'id'       => [
							'type'     => 'string',
							'required' => true,
						],
						'type'     => [
							'type'     => 'string',
							'required' => true,
						],
						'response' => [
							'type'     => 'object',
							'required' => true,
						],
					],
				],
			]
		] );
	}

	public function start_callback( \WP_REST_Request $request ): \WP_REST_Response {
		$authenticatorSelection = null;

		if ( $request['authenticatorSelection'] ) {
			$authenticatorSelection = new AuthenticatorSelectionCriteria(
				$request['authenticatorSelection']['authenticatorAttachment'] ?? null,
				$request['authenticatorSelection']['residentKey'] ?? ResidentKeyRequirement::DISCOURAGED,
				$request['authenticatorSelection']['userVerification'] ?? UserVerificationRequirement::PREFERRED
			);
		}

		$creation_options = $this->options_factory->make( wp_get_current_user(), $authenticatorSelection );

		if ( ! $creation_options->is_success() ) {
			return $creation_options->as_rest_response();
		}

		$persisted = $this->session_storage->persist_creation_options( $creation_options->get_data() );

		if ( ! $persisted->is_success() ) {
			return $persisted->as_rest_response();
		}

		$response = $creation_options->as_rest_response();
		$response->add_link(
			\ITSEC_Lib_REST::get_link_relation( 'webauthn-create-credential' ),
			rest_url( sprintf( '%s/%s/%s/create', $this->namespace, $this->rest_base, \ITSEC_Lib::url_safe_b64_encode( $persisted->get_data() ) ) )
		);

		return $response;
	}

	public function register_callback( \WP_REST_Request $request ): \WP_REST_Response {
		$token            = \ITSEC_Lib::url_safe_b64_decode( $request['token'] );
		$creation_options = $this->session_storage->get_creation_options( $token );

		if ( ! $creation_options->is_success() ) {
			return $creation_options->as_rest_response();
		}

		try {
			$credential = PublicKeyCredential::hydrateAttestation( $request['credential'] );
		} catch ( \Exception $e ) {
			return rest_convert_error_to_response( new \WP_Error(
				'itsec.webauthn.rest.register-credential.invalid-credential',
				__( 'The credential format is invalid.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::BAD_REQUEST ]
			) );
		}

		$created = $this->ceremony->perform(
			$creation_options->get_data(),
			$credential,
			$request['label'] ?: ''
		);

		if ( ! $created->is_success() ) {
			return $created->as_rest_response();
		}

		$route    = sprintf( '/ithemes-security/v1/webauthn/credentials/%s', $created->get_data()->get_id()->as_ascii_fast() );
		$response = rest_do_request( $route );
		$response->set_status( \WP_Http::CREATED );
		$response->header( 'Location', rest_url( $route ) );

		return $response;
	}
}

