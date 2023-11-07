<?php

namespace iThemesSecurity\WebAuthn\REST;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\AuthenticationCeremony;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredential;
use iThemesSecurity\WebAuthn\PublicKeyCredentialRequestOptions_Factory;
use iThemesSecurity\WebAuthn\PublicKeyCredentialUserEntity_Factory;
use iThemesSecurity\WebAuthn\Session_Storage;
use iThemesSecurity\WebAuthn\Verified_Credential_Tokens;

final class VerifyCredential extends \WP_REST_Controller {

	/** @var AuthenticationCeremony */
	private $ceremony;

	/** @var PublicKeyCredentialRequestOptions_Factory */
	private $options_factory;

	/** @var PublicKeyCredentialUserEntity_Factory */
	private $user_factory;

	/** @var Session_Storage */
	private $session_storage;

	/** @var Verified_Credential_Tokens */
	private $tokens;

	public function __construct(
		AuthenticationCeremony $ceremony,
		PublicKeyCredentialRequestOptions_Factory $options_factory,
		PublicKeyCredentialUserEntity_Factory $user_factory,
		Session_Storage $session_storage,
		Verified_Credential_Tokens $tokens
	) {
		$this->namespace       = 'ithemes-security/rpc';
		$this->rest_base       = 'webauthn/verify-credential';
		$this->ceremony        = $ceremony;
		$this->options_factory = $options_factory;
		$this->user_factory    = $user_factory;
		$this->session_storage = $session_storage;
		$this->tokens          = $tokens;
	}

	public function register_routes() {
		register_rest_route( $this->namespace, sprintf( '/%s', $this->rest_base ), [
			'methods'             => 'POST',
			'callback'            => [ $this, 'start_callback' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'user' => [
					'type' => 'string',
				],
			],
		] );
		register_rest_route( $this->namespace, sprintf( '/%s/(?P<token>[\w\-]+)/verify', $this->rest_base ), [
			'methods'             => 'POST',
			'callback'            => [ $this, 'verify_callback' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'token'      => [
					'type'      => 'string',
					'minLength' => 1,
				],
				'user'       => [
					'type' => 'string',
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
			],
		] );
	}

	public function start_callback( \WP_REST_Request $request ): \WP_REST_Response {
		$user = $this->get_user( $request );

		if ( ! $user->is_success() ) {
			return $user->as_rest_response();
		}

		$request_options = $this->options_factory->make( $user->get_data() );

		if ( ! $request_options->is_success() ) {
			return $request_options->as_rest_response();
		}

		$persisted = $this->session_storage->persist_request_options( $request_options->get_data() );

		if ( ! $persisted->is_success() ) {
			return $persisted->as_rest_response();
		}

		$response = $request_options->as_rest_response();
		$response->add_link(
			\ITSEC_Lib_REST::get_link_relation( 'webauthn-verify-credential' ),
			rest_url( sprintf( '%s/%s/%s/verify', $this->namespace, $this->rest_base, \ITSEC_Lib::url_safe_b64_encode( $persisted->get_data() ) ) )
		);

		return $response;
	}

	public function verify_callback( \WP_REST_Request $request ): \WP_REST_Response {
		$found_wp_user = $this->get_user( $request );

		if ( ! $found_wp_user->is_success() ) {
			return $found_wp_user->as_rest_response();
		}

		$user_entity = null;
		$wp_user = $found_wp_user->get_data();

		if ( $wp_user ) {
			$get_user_entity = $this->user_factory->make( $wp_user );

			if ( ! $get_user_entity->is_success() ) {
				return $get_user_entity->as_rest_response();
			}

			$user_entity = $get_user_entity->get_data();
		}

		$token           = \ITSEC_Lib::url_safe_b64_decode( $request['token'] );
		$request_options = $this->session_storage->get_request_options( $token );

		if ( ! $request_options->is_success() ) {
			return $request_options->as_rest_response();
		}

		try {
			$credential = PublicKeyCredential::hydrateAssertion( $request['credential'] );
		} catch ( \Exception $e ) {
			return rest_convert_error_to_response( new \WP_Error(
				'itsec.webauthn.rest.verify-credential.invalid-credential',
				__( 'The credential format is invalid.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::BAD_REQUEST ]
			) );
		}

		$verified = $this->ceremony->perform(
			$request_options->get_data(),
			$credential,
			$user_entity
		);

		if ( ! $verified->is_success() ) {
			return $verified->as_rest_response();
		}

		$verified_token = $this->tokens->create_token( $verified->get_data() );

		if ( ! $verified_token->is_success() ) {
			return $verified_token->as_rest_response();
		}
		if ( ! $wp_user ) {
			$found_wp_user_by_credential = $this->user_factory->find_user_by_id(
				$verified->get_data()->get_user()
			);
			if ( ! $found_wp_user_by_credential->is_success() ) {
				return $found_wp_user_by_credential->as_rest_response();
			}
			$wp_user = $found_wp_user_by_credential->get_data();
		}

		return new \WP_REST_Response( [
			'token' => $verified_token->get_data(),
			'user'  => \ITSEC_Lib_Login::get_identifier_for_user( $wp_user ),
		] );
	}

	/**
	 * Gets the requested user object.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return Result<\WP_User|null>
	 */
	private function get_user( \WP_REST_Request $request ): Result {
		if ( ! $request['user'] ) {
			return Result::success();
		}

		$user = \ITSEC_Lib_Login::get_user( $request['user'] );

		if ( $user ) {
			return Result::success( $user );
		}

		return Result::error( new \WP_Error(
			'itsec.webauthn.rest.verify-credential.user-not-found',
			\ITSEC_Lib_Login::get_not_found_error_message(),
			[ 'status' => \WP_Http::BAD_REQUEST ]
		) );
	}
}

