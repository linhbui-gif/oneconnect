<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class AuthenticatorAttestationResponse extends AuthenticatorResponse {

	/**
	 * This attribute contains an attestation object, which is opaque to,
	 * and cryptographically protected against tampering by, the client.
	 * The attestation object contains both authenticator data and an
	 * attestation statement.
	 *
	 * @var BinaryString
	 */
	protected $attestationObject;

	/**
	 * This contains a sequence of zero or more unique DOMStrings in lexicographical order.
	 * These values are the transports that the authenticator is believed to support,
	 * or an empty sequence if the information is unavailable.
	 *
	 * The values SHOULD be members of AuthenticatorTransport but Relying Parties SHOULD
	 * accept and store unknown values.
	 *
	 * @var string[]
	 */
	protected $transports;

	public function __construct( BinaryString $clientDataJSON, BinaryString $attestationObject, array $transports ) {
		Assert::thatAll( $transports )->string();

		parent::__construct( $clientDataJSON );
		$this->attestationObject = $attestationObject;
		$this->transports        = $transports;
	}

	public static function hydrate( array $data ): AuthenticatorResponse {
		Assert::that( $data, 'AuthenticatorAttestationResponse hydration does not contain "%s".' )
		      ->keyExists( 'clientDataJSON' )
		      ->keyExists( 'attestationObject' );

		return new self(
			BinaryString::from_ascii( $data['clientDataJSON'] ),
			BinaryString::from_ascii( $data['attestationObject'] ),
			$data['transports'] ?? []
		);
	}

	public function get_attestation_object(): BinaryString {
		return $this->attestationObject;
	}

	public function get_transports(): array {
		return $this->transports;
	}

	public function jsonSerialize(): array {
		return \ITSEC_Lib::recursively_json_serialize( get_object_vars( $this ) );
	}
}

