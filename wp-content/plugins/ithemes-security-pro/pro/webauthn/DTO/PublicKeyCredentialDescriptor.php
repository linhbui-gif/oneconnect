<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class PublicKeyCredentialDescriptor implements \JsonSerializable {

	/**
	 * This member contains the type of the public key credential the caller is referring to.
	 *
	 * The value SHOULD be a member of {@see PublicKeyCredentialType}.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * This member contains the credential ID of the public key credential the caller is referring to.
	 *
	 * This mirrors the rawId field of PublicKeyCredential.
	 *
	 * @var BinaryString
	 */
	protected $id;

	/**
	 * This OPTIONAL member contains a hint as to how the client might communicate with the managing
	 * authenticator of the public key credential the caller is referring to.
	 *
	 * @var string[]
	 */
	protected $transports;

	public function __construct( string $type, BinaryString $id, array $transports ) {
		Assert::that( $type )
		      ->choice( PublicKeyCredentialType::ALL, 'type "%s" is not an element of the valid values: %s' );
		Assert::thatAll( $transports )->string()
		      ->notBlank( 'transports item "%s" is blank, but was expected to contain a value.' );

		$this->type       = $type;
		$this->id         = $id;
		$this->transports = $transports;
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'PublicKeyCredentialDescriptor hydration does not contain "%s".' )
		      ->keyExists( 'type' )
		      ->keyExists( 'id' )
		      ->keyExists( 'transports' );

		return new self( $data['type'], BinaryString::from_ascii_fast( $data['id'] ), $data['transports'] );
	}

	public function get_type(): string {
		return $this->type;
	}

	public function get_id(): BinaryString {
		return $this->id;
	}

	public function get_transports(): array {
		return $this->transports;
	}

	public function jsonSerialize(): array {
		return \ITSEC_Lib::recursively_json_serialize( get_object_vars( $this ) );
	}
}
