<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialRequestOptions;

final class PublicKeyCredentialRequestOptions_Factory_Default implements PublicKeyCredentialRequestOptions_Factory {

	/** @var PublicKeyCredentialUserEntity_Factory */
	protected $user_factory;

	/** @var PublicKeyCredentialRpEntity_Factory */
	protected $rp_factory;

	/** @var PublicKeyCredential_Record_Repository */
	protected $credentials_repository;

	public function __construct(
		PublicKeyCredentialUserEntity_Factory $user_factory,
		PublicKeyCredentialRpEntity_Factory $rp_factory,
		PublicKeyCredential_Record_Repository $credentials_repository
	) {
		$this->user_factory           = $user_factory;
		$this->rp_factory             = $rp_factory;
		$this->credentials_repository = $credentials_repository;
	}

	public function make( ?\WP_User $user ): Result {
		$rp_entity   = $this->rp_factory->make();
		$user_entity = $user ? $this->user_factory->make( $user ) : null;
		$credentials = $user && $user_entity->is_success()
			? $this->credentials_repository->get_credentials_for_user( $user_entity->get_data() )
			: Result::success( [] );

		try {
			$challenge = Result::success( new BinaryString( random_bytes( 64 ) ) );
		} catch ( \Exception $e ) {
			$challenge = Result::error( new \WP_Error(
				'itsec.webauthn.request-options.no-random-bytes',
				__( 'Could not generate a random WebAuthn challenge.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		$result = Result::combine( $rp_entity, $user_entity, $credentials );

		if ( ! $result->is_success() ) {
			return Result::combine( Result::error( new \WP_Error(
				'itsec.webauthn.request-options.error',
				__( 'Could not prepare to access a WebAuthn credential.', 'it-l10n-ithemes-security-pro' )
			) ), $result );
		}

		$allow_credentials = array_map(
			function ( PublicKeyCredential_Record $record ) { return $record->as_descriptor(); },
			$credentials->get_data()
		);

		return Result::success( new PublicKeyCredentialRequestOptions(
			$challenge->get_data(),
			$rp_entity->get_data()->get_id(),
			$allow_credentials
		) );
	}
}
