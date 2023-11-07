<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;
use iThemesSecurity\Strauss\ParagonIE\ConstantTime\Base64UrlSafe;

final class BinaryString implements \JsonSerializable {

	/** @var string */
	private $value;

	/** @var string */
	private $ascii;

	public function __construct( string $value ) {
		Assert::that( $value )->notEmpty();
		$this->value = $value;
	}

	public static function from_ascii_fast( string $ascii ): self {
		$self        = new self( \ITSEC_Lib::url_safe_b64_decode( $ascii ) );
		$self->ascii = $ascii;

		return $self;
	}

	public static function from_ascii( string $ascii ): self {
		$self        = new self( Base64UrlSafe::decode( $ascii ) );
		$self->ascii = $ascii;

		return $self;
	}

	public function as_ascii_fast(): string {
		if ( $this->ascii === null ) {
			$this->ascii = \ITSEC_Lib::url_safe_b64_encode( $this->value );
		}

		return $this->ascii;
	}

	public function as_ascii(): string {
		if ( $this->ascii === null ) {
			$this->ascii = Base64UrlSafe::encode( $this->value );
		}

		return $this->ascii;
	}

	public function equals( BinaryString $other ): bool {
		return hash_equals( $this->value, $other->value );
	}

	public function get_binary(): string {
		return $this->value;
	}

	public function jsonSerialize(): string {
		return $this->as_ascii();
	}

	public function __debugInfo() {
		return [
			'value' => $this->as_ascii(),
		];
	}
}
