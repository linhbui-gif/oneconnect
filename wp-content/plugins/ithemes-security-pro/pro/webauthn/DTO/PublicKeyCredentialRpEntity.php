<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class PublicKeyCredentialRpEntity extends PublicKeyCredentialEntity {

	/**
	 * A human-palatable identifier for the Relying Party, intended only for display.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * A unique identifier for the Relying Party entity, which sets the RP ID.
	 *
	 * An RP ID is based on a host's domain name. It does not itself include a
	 * scheme or port, as an origin does. The RP ID of a public key credential
	 * determines its scope.
	 *
	 * For example: login.example.com
	 *
	 * @var string
	 */
	protected $id;

	public function __construct( string $id, string $name ) {
		Assert::that( $id )->notBlank( 'id "%s" is blank, but was expected to contain a value.' );

		$this->id = $id;
		parent::__construct( $name );
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'PublicKeyCredentialRpEntity hydration does not contain "%s".' )
		      ->keyExists( 'id' )
		      ->keyExists( 'name' );

		return new self( $data['id'], $data['name'] );
	}

	public function get_id(): string {
		return $this->id;
	}

	public function jsonSerialize(): array {
		return get_object_vars( $this );
	}
}
