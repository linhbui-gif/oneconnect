<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Cose\Key\Key;
use iThemesSecurity\Strauss\ParagonIE\ConstantTime\Base64UrlSafe;

final class PublicKey implements \JsonSerializable {

	/** @var array */
	private $data;

	public function __construct( array $data ) {
		$this->data = $data;
	}

	public static function hydrate( array $data ): self {
		$decoded = [];

		foreach ( $data as $k => $v ) {
			$decoded[ $k ] = Base64UrlSafe::decode( $v );
		}

		return new self( $decoded );
	}

	public static function from_cose_key( Key $key ): self {
		return new self( $key->getData() );
	}

	public function get_data(): array {
		return $this->data;
	}

	public function jsonSerialize(): array {
		$encoded = [];

		foreach ( $this->data as $k => $v ) {
			$encoded[ $k ] = Base64UrlSafe::encode( $v );
		}

		return $encoded;
	}
}
