<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Strauss\Cose\Algorithm\Manager as CoseManager;
use iThemesSecurity\Strauss\Cose\Algorithm\Signature\ECDSA\ECDSA;
use iThemesSecurity\Strauss\Cose\Algorithm\Signature\ECDSA\ECSignature;
use iThemesSecurity\Strauss\Cose\Key\Key;
use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\AuthenticatorAssertionResponse;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\DTO\CollectedClientData;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredential;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialDescriptor;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialRequestOptions;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialRpEntity;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialUserEntity;
use iThemesSecurity\WebAuthn\DTO\UserVerificationRequirement;

final class AuthenticationCeremony {

	/** @var PublicKeyCredentialRpEntity_Factory */
	protected $rp_factory;

	/** @var AuthenticatorDataLoader */
	protected $auth_data_loader;

	/** @var PublicKeyCredential_Record_Repository */
	protected $repository;

	/** @var CoseManager */
	private $algorithm_manager;

	public function __construct(
		PublicKeyCredentialRpEntity_Factory $rp_factory,
		AuthenticatorDataLoader $auth_data_loader,
		PublicKeyCredential_Record_Repository $repository,
		CoseManager $algorithm_manager
	) {
		$this->rp_factory        = $rp_factory;
		$this->auth_data_loader  = $auth_data_loader;
		$this->repository        = $repository;
		$this->algorithm_manager = $algorithm_manager;
	}

	/**
	 * Performs an Authentication Ceremony.
	 *
	 * See https://w3c.github.io/webauthn/#sctn-verifying-assertion.
	 * Steps 1-4 are performed by the JS client.
	 *
	 * @param PublicKeyCredentialRequestOptions                   $options
	 * @param PublicKeyCredential<AuthenticatorAssertionResponse> $credential
	 * @param PublicKeyCredentialUserEntity|null                  $user
	 *
	 * @return Result<PublicKeyCredential_Record>
	 */
	public function perform(
		PublicKeyCredentialRequestOptions $options,
		PublicKeyCredential $credential,
		?PublicKeyCredentialUserEntity $user
	): Result {
		try {
			$response = $credential->get_response();
			$rp       = $this->rp_factory->make();

			if ( ! $rp->is_success() ) {
				return $rp;
			}

			// Step 5
			if ( ! $this->is_credential_allowed( $options->get_allowed_credentials(), $credential->get_id() ) ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.authentication-ceremony.credential-not-allowed',
					__( 'The credential is not allowed.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 6
			$found_record = $this->repository->find_by_id( $credential->get_id() );

			if ( ! $found_record->is_success() ) {
				return $found_record;
			}

			$record      = $found_record->get_data();
			$user_handle = $response->get_user_handle();

			// Step 6a
			if ( $user ) {
				if ( ! $user->get_id()->equals( $record->get_user() ) ) {
					return Result::error( new \WP_Error(
						'itsec.webauthn.authentication-ceremony.wrong-credential-for-user',
						__( 'The credential does not belong to the requested user.', 'it-l10n-ithemes-security-pro' )
					) );
				}

				if ( $user_handle && ! $user_handle->equals( $user->get_id() ) ) {
					return Result::error( new \WP_Error(
						'itsec.webauthn.authentication-ceremony.user-mismatch',
						__( 'The identified user is not the requested user.', 'it-l10n-ithemes-security-pro' )
					) );
				}
			} else { // Step 6b
				if ( ! $user_handle ) {
					return Result::error( new \WP_Error(
						'itsec.webauthn.authentication-ceremony.no-user-identified',
						__( 'User not identified.', 'it-l10n-ithemes-security-pro' )
					) );
				}

				if ( ! $user_handle->equals( $record->get_user() ) ) {
					return Result::error( new \WP_Error(
						'itsec.webauthn.authentication-ceremony.wrong-credential-for-user',
						__( 'The credential does not belong to the identified user.', 'it-l10n-ithemes-security-pro' )
					) );
				}
			}

			// Step 7
			$credential_public_key = $record->get_public_key();

			// Step 8
			$auth_data = $this->auth_data_loader->load( $response->get_authenticator_data() );
			$sig       = $response->get_signature();

			// Step 9 and 10
			$client_data = $response->get_and_decode_client_data();

			// Step 11
			if ( $client_data->get_type() !== CollectedClientData::TYPE_GET ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.authentication-ceremony.invalid-type',
					__( 'The assertion type is incorrect.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 12
			if ( ! $options->get_challenge()->equals( $client_data->get_challenge() ) ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.authentication-ceremony.invalid-challenge',
					__( 'The challenge is incorrect.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 13
			if ( ! $this->do_origins_match( $rp->get_data(), $client_data->get_origin() ) ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.authentication-ceremony.invalid-origin',
					__( 'The origin is invalid.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 14
			$rp_id_hash = hash( 'sha256', $rp->get_data()->get_id(), true );

			if ( ! hash_equals( $rp_id_hash, $auth_data->get_rp_id_hash()->get_binary() ) ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.authentication-ceremony.rp-id-hash-mismatch',
					__( 'The Relying Party id hash does not match.', 'it-l10n-ithemes-security-pro' )
				) );
			}


			// Step 15
			if ( ! $auth_data->is_user_present() ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.authentication-ceremony.user-not-present',
					__( 'The user is not present.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Step 16
			if (
				$options->get_user_verification() === UserVerificationRequirement::REQUIRED &&
				! $auth_data->is_user_verified()
			) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.authentication-ceremony.user-not-verified',
					__( 'The user is not verified.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			// Skip step 17. We do not have backup requirements.
			// Skip step 18. We do not query any client extensions.

			// Step 19
			$hash = hash( 'sha256', $response->get_client_data_json()->get_binary(), true );

			// Step 20
			$data_to_sign = $response->get_authenticator_data()->get_binary() . $hash;
			$key          = Key::createFromData( $credential_public_key->get_data() );
			$algorithm    = $this->algorithm_manager->get( $key->alg() );

			if ( $algorithm instanceof ECDSA ) {
				$binary_signature = $this->fix_signature( $sig->get_binary() );
			} else {
				$binary_signature = $sig->get_binary();
			}

			if ( ! $algorithm->verify( $data_to_sign, $key, $binary_signature ) ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.authentication-ceremony.invalid-signature',
					__( 'The signature is invalid.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			$record->set_eligible_for_backups( $auth_data->is_eligible_for_backups() );
			$record->set_backed_up( $auth_data->is_backed_up() );

			$sign_count = $auth_data->get_sign_count();

			if ( $sign_count > 0 && $sign_count <= $record->get_signature_count() ) {
				return Result::error( new \WP_Error(
					'itsec.webauthn.authentication-ceremony.invalid-sign-count',
					__( 'The signature count is invalid.', 'it-l10n-ithemes-security-pro' )
				) );
			}

			$record->record_use( $sign_count );

			$this->repository->persist( $record );
		} catch ( \Throwable $e ) {
			\ITSEC_Log::add_error( 'webauthn', 'authentication-ceremony-failed', [
				'user'      => $user ? $user->get_id()->as_ascii_fast() : '',
				'exception' => $e->getMessage(),
			] );

			return Result::error( new \WP_Error(
				'itsec.webauthn.authentication-ceremony.failed',
				__( 'Could not authenticate with a WebAuthn credential.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		return Result::success( $record );
	}

	/**
	 * Checks if the used credential is allowed according to the Request Options.
	 *
	 * @param PublicKeyCredentialDescriptor[] $allowed_credentials
	 * @param BinaryString                    $credential_id
	 *
	 * @return bool
	 */
	private function is_credential_allowed( array $allowed_credentials, BinaryString $credential_id ): bool {
		if ( ! $allowed_credentials ) {
			return true;
		}

		foreach ( $allowed_credentials as $credential ) {
			if ( $credential->get_id()->equals( $credential_id ) ) {
				return true;
			}
		}

		return false;
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
	 * For legacy compatibility reasons, ES256 signatures are wrapped in
	 * ASN 1 form. Thus, we have to convert back to the unwrapped value.
	 *
	 * https://w3c.github.io/webauthn/#sctn-signature-attestation-types
	 *
	 * @param string $signature
	 *
	 * @return string
	 */
	private function fix_signature( string $signature ): string {
		if ( mb_strlen( $signature, '8bit' ) === 64 ) {
			return $signature;
		}

		return ECSignature::fromAsn1( $signature, 64 );
	}
}
