<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Strauss\Cose\Algorithm\Manager as CoseManager;
use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\AuthenticatorSelectionCriteria;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialCreationOptions;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialParameters;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialType;

final class PublicKeyCredentialCreationOptions_Factory_Default implements PublicKeyCredentialCreationOptions_Factory {

	/** @var PublicKeyCredentialUserEntity_Factory */
	private $user_factory;

	/** @var PublicKeyCredentialRpEntity_Factory */
	private $rp_factory;

	/** @var PublicKeyCredential_Record_Repository */
	private $credentials_repository;

	/** @var CoseManager */
	private $algorithm_manager;

	public function __construct(
		PublicKeyCredentialUserEntity_Factory $user_factory,
		PublicKeyCredentialRpEntity_Factory $rp_factory,
		PublicKeyCredential_Record_Repository $credentials_repository,
		CoseManager $algorithm_manager
	) {
		$this->user_factory           = $user_factory;
		$this->rp_factory             = $rp_factory;
		$this->credentials_repository = $credentials_repository;
		$this->algorithm_manager      = $algorithm_manager;
	}

	public function make( \WP_User $user, AuthenticatorSelectionCriteria $authenticator_selection = null ): Result {
		$rp_entity   = $this->rp_factory->make();
		$user_entity = $this->user_factory->make( $user );
		$credentials = $user_entity->is_success()
			? $this->credentials_repository->get_credentials_for_user( $user_entity->get_data() )
			: null;

		try {
			$challenge = Result::success( new BinaryString( random_bytes( 64 ) ) );
		} catch ( \Exception $e ) {
			$challenge = Result::error( new \WP_Error(
				'itsec.webauthn.creation-options.no-random-bytes',
				__( 'Could not generate a random WebAuthn challenge.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		$result = Result::combine( $user_entity, $rp_entity, $credentials, $challenge );

		if ( ! $result->is_success() ) {
			return Result::combine( Result::error( new \WP_Error(
				'itsec.webauthn.creation-options.error',
				__( 'Could not prepare to register a WebAuthn credential.', 'it-l10n-ithemes-security-pro' )
			) ), $result );
		}

		$pub_key_cred_params = [];

		foreach ( $this->algorithm_manager->list() as $id ) {
			$pub_key_cred_params[] = new PublicKeyCredentialParameters(
				PublicKeyCredentialType::PUBLIC_KEY,
				$id
			);
		}

		$exclude_credentials = array_map(
			function ( PublicKeyCredential_Record $record ) { return $record->as_descriptor(); },
			$credentials->get_data()
		);

		return Result::success( new PublicKeyCredentialCreationOptions(
			$rp_entity->get_data(),
			$user_entity->get_data(),
			$challenge->get_data(),
			$pub_key_cred_params,
			$exclude_credentials,
			$authenticator_selection
		) );
	}
}
