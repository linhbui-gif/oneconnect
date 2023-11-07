<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

class AttestationStatement implements \JsonSerializable {
	const TYPE_NONE = 'none';
	const TYPE_BASIC = 'basic';
	const TYPE_SELF = 'self';
	const TYPE_ATTCA = 'attca';
	const TYPE_ECDAA = 'ecdaa';
	const TYPE_ANONCA = 'anonca';

	protected $fmt;
	protected $attStmt;
	protected $type;

	public function __construct(
		string $fmt,
		array $attStmt,
		string $type
	) {
		$this->fmt     = $fmt;
		$this->attStmt = $attStmt;
		$this->type    = $type;
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'AttestationStatement hydration does not contain "%s".' )
		      ->keyExists( 'fmt' )
		      ->keyExists( 'attStmt' )
		      ->keyExists( 'type' );

		return new self(
			$data['fmt'],
			$data['attStmt'],
			$data['type']
		);
	}

	public function get_fmt(): string {
		return $this->fmt;
	}

	public function get_att_stmt(): array {
		return $this->attStmt;
	}

	public function has( string $key ): bool {
		return array_key_exists( $key, $this->attStmt );
	}

	public function get( string $key ) {
		return $this->attStmt[ $key ];
	}

	public function get_type(): string {
		return $this->type;
	}

	public function jsonSerialize(): array {
		return get_object_vars( $this );
	}
}
