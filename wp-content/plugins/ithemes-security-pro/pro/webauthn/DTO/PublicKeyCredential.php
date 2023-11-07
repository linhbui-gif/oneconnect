<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

/**
 * @template T of AuthenticatorResponse
 */
final class PublicKeyCredential implements \JsonSerializable {

	/** @var BinaryString */
	protected $id;

	/**
	 * A member of {@see PublicKeyCredentialType}.
	 *
	 * @var string
	 */
	protected $type;

	/** @var AuthenticatorResponse */
	protected $response;

	public function __construct( BinaryString $id, string $type, AuthenticatorResponse $response ) {
		Assert::that( $type )
		      ->choice( PublicKeyCredentialType::ALL, 'type "%s" is not an element of the valid values: %s' );

		$this->id       = $id;
		$this->type     = $type;
		$this->response = $response;
	}

	/**
	 * Hydrates a Credential for an attestation response.
	 *
	 * @param array $data
	 *
	 * @return PublicKeyCredential<AuthenticatorAttestationResponse>
	 */
	public static function hydrateAttestation( array $data ): self {
		return self::hydrate( $data, AuthenticatorAttestationResponse::class );
	}

	/**
	 * Hydrates a Credential for an assertion response.
	 *
	 * @param array $data
	 *
	 * @return PublicKeyCredential<AuthenticatorAssertionResponse>
	 */
	public static function hydrateAssertion( array $data ): self {
		return self::hydrate( $data, AuthenticatorAssertionResponse::class );
	}

	/**
	 * Hydrates a Credential.
	 *
	 * @param array                               $data
	 * @param class-string<AuthenticatorResponse> $response_type
	 *
	 * @return static
	 */
	protected static function hydrate( array $data, string $response_type ): self {
		Assert::that( $data, 'PublicKeyCredential hydration does not contain "%s".' )
		      ->keyExists( 'id' )
		      ->keyExists( 'type' )
		      ->keyExists( 'response' );

		return new self(
			BinaryString::from_ascii_fast( $data['id'] ),
			$data['type'],
			$response_type::hydrate( $data['response'] )
		);
	}

	public function get_id(): BinaryString {
		return $this->id;
	}

	public function get_type(): string {
		return $this->type;
	}

	/** @return T */
	public function get_response(): AuthenticatorResponse {
		return $this->response;
	}

	public function jsonSerialize(): array {
		return \ITSEC_Lib::recursively_json_serialize( get_object_vars( $this ) );
	}
}
