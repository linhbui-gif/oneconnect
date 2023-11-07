<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class PublicKeyCredentialCreationOptions implements \JsonSerializable {

	/** @var PublicKeyCredentialRpEntity */
	protected $rp;

	/** @var PublicKeyCredentialUserEntity */
	protected $user;

	/**
	 * This member specifies a challenge that the authenticator signs,
	 * along with other data, when producing an attestation object
	 * for the newly created credential.
	 *
	 * In order to prevent replay attacks, the challenges MUST contain
	 * enough entropy to make guessing them infeasible. Challenges SHOULD
	 * therefore be at least 16 bytes long.
	 *
	 * @var BinaryString
	 */
	protected $challenge;

	/**
	 * This member lists the key types and signature algorithms the
	 * Relying Party supports, ordered from most preferred to least preferred.
	 *
	 * @var PublicKeyCredentialParameters[]
	 */
	protected $pubKeyCredParams;

	/**
	 * The Relying Party SHOULD use this OPTIONAL member to list any existing
	 * credentials mapped to this user account (as identified by user.id).
	 * This ensures that the new credential is not created on an authenticator
	 * that already contains a credential mapped to this user account.
	 *
	 * @var PublicKeyCredentialDescriptor[]
	 */
	protected $excludeCredentials;

	/**
	 * The Relying Party MAY use this OPTIONAL member to specify capabilities
	 * and settings that the authenticator MUST or SHOULD satisfy to
	 * participate in the create() operation.
	 *
	 * @var AuthenticatorSelectionCriteria|null
	 */
	protected $authenticatorSelection;

	/**
	 * The Relying Party MAY use this OPTIONAL member to specify a preference
	 * regarding attestation conveyance. Its value SHOULD be a member of
	 * {@see AttestationConveyancePreference}.
	 *
	 * @var string
	 */
	protected $attestation;

	public function __construct(
		PublicKeyCredentialRpEntity $rp,
		PublicKeyCredentialUserEntity $user,
		BinaryString $challenge,
		array $pubKeyCredParams,
		array $excludeCredentials = [],
		AuthenticatorSelectionCriteria $authenticatorSelection = null,
		string $attestation = AttestationConveyancePreference::NONE
	) {
		Assert::that( $pubKeyCredParams )->minCount(
			1,
			'pubKeyCredParams should have at least %d elements, but has %d elements.'
		);
		Assert::thatAll( $pubKeyCredParams )->isInstanceOf( PublicKeyCredentialParameters::class );
		Assert::thatAll( $excludeCredentials )->isInstanceOf( PublicKeyCredentialDescriptor::class );
		Assert::that( $attestation )->choice(
			AttestationConveyancePreference::ALL,
			'attestation "%s" is not an element of the valid values: %s'
		);

		$this->rp                     = $rp;
		$this->user                   = $user;
		$this->challenge              = $challenge;
		$this->pubKeyCredParams       = $pubKeyCredParams;
		$this->excludeCredentials     = $excludeCredentials;
		$this->authenticatorSelection = $authenticatorSelection;
		$this->attestation            = $attestation;
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'PublicKeyCredentialCreationOptions hydration does not contain "%s".' )
		      ->keyExists( 'rp' )
		      ->keyExists( 'user' )
		      ->keyExists( 'challenge' )
		      ->keyExists( 'pubKeyCredParams' )
		      ->keyExists( 'excludeCredentials' );
		Assert::that( $data['pubKeyCredParams'] )
		      ->isArray( 'pubKeyCredParams "%s" is not an array.' );
		Assert::thatAll( $data['pubKeyCredParams'] )
		      ->isArray( 'pubKeyCredParams item "%s" is not an array.' );
		Assert::that( $data['excludeCredentials'] )
		      ->isArray( 'excludeCredentials "%s" is not an array.' );
		Assert::thatAll( $data['excludeCredentials'] )
		      ->isArray( 'excludeCredentials item "%s" is not an array.' );
		Assert::thatNullOr( $data['authenticatorSelection'] ?? null )
		      ->isArray( 'authenticatorSelection "%s" is not an array.' );

		return new self(
			PublicKeyCredentialRpEntity::hydrate( $data['rp'] ),
			PublicKeyCredentialUserEntity::hydrate( $data['user'] ),
			BinaryString::from_ascii( $data['challenge'] ),
			array_map( [ PublicKeyCredentialParameters::class, 'hydrate' ], $data['pubKeyCredParams'] ),
			array_map( [ PublicKeyCredentialDescriptor::class, 'hydrate' ], $data['excludeCredentials'] ),
			isset( $data['authenticatorSelection'] )
				? AuthenticatorSelectionCriteria::hydrate( $data['authenticatorSelection'] )
				: null,
			$data['attestation']
		);
	}

	public function get_rp(): PublicKeyCredentialRpEntity {
		return $this->rp;
	}

	public function get_user(): PublicKeyCredentialUserEntity {
		return $this->user;
	}

	public function get_challenge(): BinaryString {
		return $this->challenge;
	}

	/** @return PublicKeyCredentialParameters[] */
	public function get_pub_key_cred_params(): array {
		return $this->pubKeyCredParams;
	}

	public function get_excluded_credentials(): array {
		return $this->excludeCredentials;
	}

	public function get_authenticator_selection(): ?AuthenticatorSelectionCriteria {
		return $this->authenticatorSelection;
	}

	public function get_attestation(): string {
		return $this->attestation;
	}

	public function jsonSerialize(): array {
		return array_filter(
			\ITSEC_Lib::recursively_json_serialize( get_object_vars( $this ) ),
			function ( $value ) {
				return ! is_null( $value );
			}
		);
	}
}
