<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class AuthenticatorData implements \JsonSerializable {
	private const FLAG_UP = 0b00000001;
	private const FLAG_F1 = 0b00000010;
	private const FLAG_UV = 0b00000100;
	private const FLAG_BE = 0b00001000;
	private const FLAG_BS = 0b00010000;
	private const FLAG_F2 = 0b00100000;
	private const FLAG_AT = 0b01000000;
	private const FLAG_ED = 0b10000000;

	/**
	 * Raw authenticator data.
	 *
	 * @var string
	 */
	protected $authData;

	/**
	 * SHA-256 hash of the RP ID the credential is scoped to.
	 *
	 * @var BinaryString
	 */
	protected $rpIdHash;

	/** @var string */
	protected $flags;

	/** @var int */
	protected $signCount;

	/** @var AttestedCredentialData|null */
	protected $attestedCredentialData;

	public function __construct(
		string $authData,
		BinaryString $rpIdHash,
		string $flags,
		int $signCount,
		?AttestedCredentialData $attestedCredentialData
	) {
		$this->authData               = $authData;
		$this->rpIdHash               = $rpIdHash;
		$this->flags                  = $flags;
		$this->signCount              = $signCount;
		$this->attestedCredentialData = $attestedCredentialData;
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'AuthenticatorData hydration does not contain "%s".' )
		      ->keyExists( 'authData' )
		      ->keyExists( 'rpIdHash' )
		      ->keyExists( 'flags' )
		      ->keyExists( 'signCount' );

		return new self(
			$data['authData'],
			$data['rpIdHash'],
			$data['flags'],
			$data['signCount'],
			isset( $data['attestedCredentialData'] )
				? AttestedCredentialData::hydrate( $data['attestedCredentialData'] )
				: null
		);
	}

	public function get_auth_data(): string {
		return $this->authData;
	}

	public function get_rp_id_hash(): BinaryString {
		return $this->rpIdHash;
	}

	public function is_user_present(): bool {
		return 0 !== ( ord( $this->flags ) & self::FLAG_UP );
	}

	public function is_user_verified(): bool {
		return 0 !== ( ord( $this->flags ) & self::FLAG_UV );
	}

	public function has_attested_credential_data(): bool {
		return 0 !== ( ord( $this->flags ) & self::FLAG_AT );
	}

	public function is_eligible_for_backups(): bool {
		return 0 !== ( ord( $this->flags ) & self::FLAG_BE );
	}

	public function is_backed_up(): bool {
		return 0 !== ( ord( $this->flags ) & self::FLAG_BS );
	}

	public function get_sign_count(): int {
		return $this->signCount;
	}

	public function get_attested_credential_data(): ?AttestedCredentialData {
		return $this->attestedCredentialData;
	}

	public function jsonSerialize(): array {
		$data = get_object_vars( $this );
		unset( $data['authData'] );

		return array_filter(
			\ITSEC_Lib::recursively_json_serialize( $data ),
			function ( $value ) {
				return ! is_null( $value );
			}
		);
	}
}
