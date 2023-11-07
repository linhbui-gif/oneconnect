<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Strauss\Cose\Key\Key;
use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\AttestationObject;
use iThemesSecurity\WebAuthn\DTO\AttestationStatement;
use iThemesSecurity\WebAuthn\DTO\AuthenticatorAttestationResponse;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\DTO\CollectedClientData;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredential;
use iThemesSecurity\WebAuthn\DTO\PublicKey;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialCreationOptions;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialParameters;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialRpEntity;
use iThemesSecurity\WebAuthn\DTO\UserVerificationRequirement;

final class RegistrationCeremony {

	/** @var PublicKeyCredentialRpEntity_Factory */
	protected $rp_factory;

	/** @var AttestationObjectLoader */
	protected $attestation_loader;

	/** @var PublicKeyCredential_Record_Repository */
	protected $repository;

	public function __construct(
		PublicKeyCredentialRpEntity_Factory $rp_factory,
		AttestationObjectLoader $attestation_loader,
		PublicKeyCredential_Record_Repository $repository
	) {
		$this->rp_factory         = $rp_factory;
		$this->attestation_loader = $attestation_loader;
		$this->repository         = $repository;
	}

	/**
	 * Performs a Registration Ceremony.
	 *
	 * See https://w3c.github.io/webauthn/#sctn-registering-a-new-credential.
	 * Steps 1-5 are performed by the JS client.
	 *
	 * @param PublicKeyCredentialCreationOptions                    $options    The options used to initialize the ceremony.
	 * @param PublicKeyCredential<AuthenticatorAttestationResponse> $credential The Attestation response from the Authenticator.
	 * @param string                                                $label      A label created by the user to identify this credential / device.
	 *
	 * @return Result<PublicKeyCredential_Record>
	 */
	public function perform(
		PublicKeyCredentialCreationOptions $options,
		PublicKeyCredential $credential,
		string $label
	): Result {
		try {
			$response = $credential->get_response();
			$rp       = $this->rp_factory->make();

			if ( ! $rp->is_success() ) {
				return $rp;
			}

			// Step 6.
			$client_data = $response->get_and_decode_client_data();

			// Step 7
			if ( $client_data->get_type() !== CollectedClientData::TYPE_CREATE ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.invalid-type',
					__( 'The attestation type is incorrect.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 8
			if ( ! $options->get_challenge()->equals( $client_data->get_challenge() ) ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.invalid-challenge',
					__( 'The challenge is incorrect.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 9
			if ( ! $this->do_origins_match( $rp->get_data(), $client_data->get_origin() ) ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.invalid-origin',
					__( 'The origin is invalid.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Skip Step 10. We only use the None attestation.

			// Step 11
			$attestation = $this->attestation_loader->load( $response->get_attestation_object() );

			// Step 12
			$rp_id_hash = hash( 'sha256', $rp->get_data()->get_id(), true );

			if ( ! hash_equals( $rp_id_hash, $attestation->get_authenticator_data()->get_rp_id_hash()->get_binary() ) ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.rp-id-hash-mismatch',
					__( 'The Relying Party id hash does not match.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 13
			if ( ! $attestation->get_authenticator_data()->is_user_present() ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.user-not-present',
					__( 'The user is not present.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			$authenticator_selection = $options->get_authenticator_selection();

			// Step 14
			if (
				$authenticator_selection &&
				$authenticator_selection->get_user_verification() === UserVerificationRequirement::REQUIRED &&
				! $attestation->get_authenticator_data()->is_user_verified()
			) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.user-not-verified',
					__( 'The user is not verified.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Skip Steps 15 and 16. We do not have backup requirements.

			// Step 17
			$algorithm = $this->get_credential_algorithm( $attestation );

			if ( ! $algorithm ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.no-alg',
					__( 'Cannot determine algorithm used.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			$allowed_algorithms = array_map(
				function ( PublicKeyCredentialParameters $p ) { return $p->get_alg(); },
				$options->get_pub_key_cred_params()
			);
			if ( ! in_array( $algorithm, $allowed_algorithms, true ) ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.invalid-alg',
					__( 'An unsupported algorithm is provided.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Skip Step 18, we do not query any client extensions.

			// Step 19 - 22. We only use the None attestation.
			if ( $attestation->get_attestation_statement()->get_type() !== AttestationStatement::TYPE_NONE ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.invalid-attestation-stmt-type',
					__( 'Invalid attestation statement type.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 23.
			$credential_id = $this->get_credential_id( $attestation );

			if ( ! $credential_id ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.missing-credential-id',
					__( 'No credential id given.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			if ( strlen( $credential_id->get_binary() ) > 1023 ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.credential-id-too-long',
					__( 'Credential id is too long.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 24.
			$is_available = $this->repository->is_id_available( $credential_id );

			if ( ! $is_available->is_success() || ! $is_available->get_data() ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.registration-ceremony.duplicate-credential-id',
					__( 'Credential id is already registered.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			$public_key = Key::createFromData( $this->get_credential_public_key( $attestation ) );

			$record = new PublicKeyCredential_Record(
				$credential_id,
				$credential->get_type(),
				$response->get_transports(),
				PublicKey::from_cose_key( $public_key ),
				$attestation->get_authenticator_data()->get_sign_count(),
				$attestation->get_authenticator_data()->is_eligible_for_backups(),
				$attestation->get_authenticator_data()->is_backed_up(),
				$options->get_user()->get_id(),
				new \DateTimeImmutable( 'now', new \DateTimeZone( 'UTC' ) ),
				$label,
				PublicKeyCredential_Record::S_ACTIVE
			);

			$persisted = $this->repository->persist( $record );

			if ( ! $persisted->is_success() ) {
				return $persisted;
			}

			return Result::success( $record );
		} catch ( \Throwable $e ) {
			\ITSEC_Log::add_error( 'webauthn', 'registration-ceremony-failed', [
				'user'      => $options->get_user()->get_id()->as_ascii_fast(),
				'exception' => $e->getMessage(),
			] );

			return Result::error( new \WP_Error(
				'itsec.webauthn.registration-ceremony.failed',
				__( 'Could not register a new WebAuthn credential.', 'it-l10n-ithemes-security-pro' )
			) );
		}
	}

	/**
	 * Checks if the origin given by the Credential matches the
	 * Relying Party origin.
	 *
	 * @param PublicKeyCredentialRpEntity $rp
	 * @param string                      $given_origin
	 *
	 * @return bool
	 */
	private function do_origins_match( PublicKeyCredentialRpEntity $rp, string $given_origin ): bool {
		$parts = wp_parse_url( $given_origin );

		if ( ! empty( $parts['port'] ) ) {
			$id = sprintf( '%s:%d', $parts['host'], $parts['port'] );
		} else {
			$id = $parts['host'];
		}

		return $id === $rp->get_id();
	}

	/**
	 * Extracts the Public Key data from the Attestation object.
	 *
	 * @param AttestationObject $attestation
	 *
	 * @return array|null
	 */
	private function get_credential_public_key( AttestationObject $attestation ): ?array {
		$credential_data = $attestation->get_authenticator_data()->get_attested_credential_data();

		if ( ! $credential_data ) {
			return null;
		}

		return $credential_data->get_credential_public_key();
	}

	/**
	 * Finds the credential algorithm used by the Public Key.
	 *
	 * @param AttestationObject $attestation
	 *
	 * @return int|null
	 */
	private function get_credential_algorithm( AttestationObject $attestation ): ?int {
		$public_key = $this->get_credential_public_key( $attestation );

		if ( ! $public_key ) {
			return null;
		}

		if ( ! isset( $public_key[3] ) ) {
			return null;
		}

		return (int) $public_key[3];
	}

	/**
	 * Extracts the credential ID from the Attestation object.
	 *
	 * @param AttestationObject $attestation
	 *
	 * @return BinaryString|null
	 */
	private function get_credential_id( AttestationObject $attestation ): ?BinaryString {
		$credential_data = $attestation->get_authenticator_data()->get_attested_credential_data();

		if ( ! $credential_data ) {
			return null;
		}

		return $credential_data->get_credential_id();
	}
}
