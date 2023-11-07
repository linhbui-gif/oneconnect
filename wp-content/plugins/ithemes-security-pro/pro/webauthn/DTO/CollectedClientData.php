<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class CollectedClientData {

	const TYPE_CREATE = 'webauthn.create';
	const TYPE_GET = 'webauthn.get';

	/**
	 * This member contains the string "webauthn.create" when creating
	 * new credentials, and "webauthn.get" when getting an assertion
	 * from an existing credential. The purpose of this member is to
	 * prevent certain types of signature confusion attacks
	 * (where an attacker substitutes one legitimate signature for another).
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * This member contains the base64url encoding of the challenge provided
	 * by the Relying Party.
	 *
	 * @var BinaryString
	 */
	protected $challenge;

	/**
	 * This member contains the fully qualified origin of the requester,
	 * as provided to the authenticator by the client, in the syntax
	 * defined by [RFC6454].
	 *
	 * @var string
	 */
	protected $origin;

	/**
	 * This OPTIONAL member contains the inverse of the sameOriginWithAncestors
	 * argument value that was passed into the internal method.
	 *
	 * @var bool|null
	 */
	protected $crossOrigin;

	public function __construct( string $type, BinaryString $challenge, string $origin, ?bool $crossOrigin ) {
		Assert::that( $type )
		      ->choice( [ self::TYPE_CREATE, self::TYPE_GET ], 'type "%s" is not an element of the valid values: %s' );
		Assert::that( $origin )
		      ->notBlank( 'origin "%s" is blank, but was expected to contain a value.' );

		$this->type        = $type;
		$this->challenge   = $challenge;
		$this->origin      = $origin;
		$this->crossOrigin = $crossOrigin;
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'CollectedClientData hydration does not contain "%s".' )
		      ->keyExists( 'type' )
		      ->keyExists( 'challenge' )
		      ->keyExists( 'origin' );

		return new self(
			$data['type'],
			BinaryString::from_ascii( $data['challenge'] ),
			$data['origin'],
			$data['crossOrigin'] ?? null
		);
	}

	public function get_type(): string {
		return $this->type;
	}

	public function get_challenge(): BinaryString {
		return $this->challenge;
	}

	public function get_origin(): string {
		return $this->origin;
	}

	public function is_cross_origin(): ?bool {
		return $this->crossOrigin;
	}
}
