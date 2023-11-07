<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;
use iThemesSecurity\Strauss\Cose\Algorithms;

final class PublicKeyCredentialParameters implements \JsonSerializable {

	/**
	 * This member specifies the type of credential to be created.
	 *
	 * The value SHOULD be a member of {@see PublicKeyCredentialType}.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * This member specifies the cryptographic signature algorithm with which
	 * the newly generated credential will be used, and thus also the type of
	 * asymmetric key pair to be generated, e.g., RSA or Elliptic Curve.
	 *
	 * The {@see Algorithms} contains a list of available values.
	 *
	 * @var integer
	 */
	protected $alg;

	public function __construct( string $type, int $alg ) {
		Assert::that( $type )
		      ->choice( PublicKeyCredentialType::ALL, 'type "%s" is not an element of the valid values: %s' );
		Assert::that( Algorithms::getHashAlgorithmFor( $alg ) )->notEmpty( 'alg "%s" is not a valid algorithm.' );

		$this->type = $type;
		$this->alg  = $alg;
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'PublicKeyCredentialParameters hydration does not contain "%s".' )
		      ->keyExists( 'type' )
		      ->keyExists( 'alg' );

		return new self( $data['type'], $data['alg'] );
	}

	public function get_type(): string {
		return $this->type;
	}

	public function get_alg(): int {
		return $this->alg;
	}

	public function jsonSerialize(): array {
		return get_object_vars( $this );
	}
}
