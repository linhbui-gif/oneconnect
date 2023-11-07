<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class AttestedCredentialData implements \JsonSerializable {

	/** @var string */
	protected $aaguid;

	/** @var BinaryString */
	protected $credentialId;

	/** @var array|null */
	protected $credentialPublicKey;

	public function __construct( string $aaguid, BinaryString $credentialId, ?array $credentialPublicKey ) {
		$this->aaguid              = $aaguid;
		$this->credentialId        = $credentialId;
		$this->credentialPublicKey = $credentialPublicKey;
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'AttestedCredentialData hydration does not contain "%s".' )
		      ->keyExists( 'aaguid' )
		      ->keyExists( 'credentialId' );

		return new self(
			$data['aaguid'],
			BinaryString::from_ascii_fast( $data['credentialId'] ),
			$data['credentialPublicKey'] ?? null
		);
	}

	public function get_aaguid(): string {
		return $this->aaguid;
	}

	public function get_credential_id(): BinaryString {
		return $this->credentialId;
	}

	public function get_credential_public_key(): ?array {
		return $this->credentialPublicKey;
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
