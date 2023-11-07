<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

abstract class AuthenticatorResponse implements \JsonSerializable {

	/**
	 * This attribute contains the JSON-compatible serialization of client data
	 * passed to the authenticator by the client in order to generate this credential.
	 * The exact JSON serialization MUST be preserved, as the hash of the serialized
	 * client data has been computed over it.
	 *
	 * @var BinaryString
	 */
	protected $clientDataJSON;

	public function __construct( BinaryString $clientDataJSON ) { $this->clientDataJSON = $clientDataJSON; }

	abstract public static function hydrate( array $data ): AuthenticatorResponse;

	public function get_client_data_json(): BinaryString {
		return $this->clientDataJSON;
	}

	public function get_and_decode_client_data(): CollectedClientData {
		$decoded = json_decode( $this->clientDataJSON->get_binary(), true );
		Assert::that( $decoded )->isArray();

		return CollectedClientData::hydrate( $decoded );
	}
}
