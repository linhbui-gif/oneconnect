<?php

namespace iThemesSecurity\WebAuthn\DTO;

final class AttestationObject implements \JsonSerializable {

	/** @var string */
	protected $raw;

	/** @var AttestationStatement */
	protected $attStmt;

	/** @var AuthenticatorData */
	protected $authData;

	public function __construct( string $raw, AttestationStatement $attStmt, AuthenticatorData $authData ) {
		$this->raw      = $raw;
		$this->attStmt  = $attStmt;
		$this->authData = $authData;
	}

	public function get_raw(): string {
		return $this->raw;
	}

	public function get_attestation_statement(): AttestationStatement {
		return $this->attStmt;
	}

	public function get_authenticator_data(): AuthenticatorData {
		return $this->authData;
	}

	public function jsonSerialize(): array {
		return [
			'attStmt'  => $this->attStmt->jsonSerialize(),
			'authData' => $this->authData->jsonSerialize(),
		];
	}
}
