<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Strauss\Assert\Assert;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\DTO\PublicKey;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialDescriptor;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialType;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialUserEntity;

final class PublicKeyCredential_Record implements \JsonSerializable {
	const S_ACTIVE = 'active';
	const S_TRASH = 'trash';

	/**
	 * The credential id provided by the Authenticator.
	 *
	 * @var BinaryString
	 */
	protected $id;

	/**
	 * The type of public key credential.
	 *
	 * A member of {@see PublicKeyCredentialType}.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The list of transports provided by the WebAuthn API
	 * that were used to communicate with this credential.
	 *
	 * @var string[]
	 */
	protected $transports;

	/**
	 * The public key of the credential.
	 *
	 * @var PublicKey
	 */
	protected $public_key;

	/**
	 * The number of times this credential has signed an assertion.
	 *
	 * @var int
	 */
	protected $signature_count;

	/**
	 * If the Credential can be backed up.
	 *
	 * @var bool
	 */
	protected $backup_eligible;

	/**
	 * If the credential has been backed up.
	 *
	 * @var bool
	 */
	protected $backed_up;

	/**
	 * The user this credential is associated with.
	 *
	 * The id of {@see PublicKeyCredentialUserEntity}.
	 *
	 * @var BinaryString
	 */
	protected $user;

	/**
	 * The date this credential was created.
	 *
	 * @var \DateTimeInterface
	 */
	protected $created_at;

	/**
	 * A user provided label identifying this credential.
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * The status of this credential.
	 *
	 * @var string
	 */
	protected $status;

	/**
	 * The date this passkey was last used to authenticate.
	 *
	 * @var \DateTimeInterface|null
	 */
	protected $last_used;

	/**
	 * The date this passkey was trashed.
	 *
	 * @var \DateTimeInterface|null
	 */
	protected $trashed_at;

	public function __construct(
		BinaryString $id,
		string $type,
		array $transports,
		PublicKey $public_key,
		int $signature_count,
		bool $backup_eligible,
		bool $backed_up,
		BinaryString $user,
		\DateTimeInterface $created_at,
		string $label,
		string $status,
		\DateTimeInterface $last_used = null,
		\DateTimeInterface $trashed_at = null
	) {
		Assert::that( $type )->choice(
			PublicKeyCredentialType::ALL,
			'type "%s" is not an element of the valid values: %s'
		);
		Assert::thatAll( $transports )->string()
		      ->notBlank( 'transports item "%s" is blank, but was expected to contain a value.' );

		$this->id              = $id;
		$this->type            = $type;
		$this->transports      = $transports;
		$this->public_key      = $public_key;
		$this->signature_count = $signature_count;
		$this->backup_eligible = $backup_eligible;
		$this->backed_up       = $backed_up;
		$this->user            = $user;
		$this->created_at      = $created_at;
		$this->label           = $label;
		$this->status          = $status;
		$this->last_used       = $last_used;
		$this->trashed_at      = $trashed_at;
	}

	public function get_id(): BinaryString {
		return $this->id;
	}

	public function get_type(): string {
		return $this->type;
	}

	public function get_transports(): array {
		return $this->transports;
	}

	public function get_public_key(): PublicKey {
		return $this->public_key;
	}

	public function set_signature_count( int $signature_count ) {
		$this->signature_count = $signature_count;
	}

	public function get_signature_count(): int {
		return $this->signature_count;
	}

	public function is_eligible_for_backups(): bool {
		return $this->backup_eligible;
	}

	public function set_eligible_for_backups( bool $backup_eligible ) {
		$this->backup_eligible = $backup_eligible;
	}

	public function is_backed_up(): bool {
		return $this->backed_up;
	}

	public function set_backed_up( bool $backed_up ) {
		$this->backed_up = $backed_up;
	}

	public function get_user(): BinaryString {
		return $this->user;
	}

	public function set_label( string $label ) {
		$this->label = $label;
	}

	public function get_label(): string {
		return $this->label;
	}

	public function set_status( string $status ) {
		$this->status = $status;
	}

	public function get_status(): string {
		return $this->status;
	}

	public function get_created_at(): \DateTimeInterface {
		return $this->created_at;
	}

	public function get_last_used(): ?\DateTimeInterface {
		return $this->last_used;
	}

	public function get_trashed_at(): ?\DateTimeInterface {
		return $this->trashed_at;
	}

	public function record_use( int $signature_count ): void {
		$this->set_signature_count( $signature_count );
		$this->last_used = new \DateTimeImmutable( 'now', new \DateTimeZone( 'UTC' ) );
	}

	public function trash(): void {
		$this->set_status( self::S_TRASH );
		$this->trashed_at = new \DateTimeImmutable( 'now', new \DateTimeZone( 'UTC' ) );
	}

	public function restore(): void {
		$this->set_status( self::S_ACTIVE );
		$this->trashed_at = null;
	}

	public function as_descriptor(): PublicKeyCredentialDescriptor {
		return new PublicKeyCredentialDescriptor(
			$this->type, $this->id, $this->transports
		);
	}

	public function jsonSerialize(): array {
		return \ITSEC_Lib::recursively_json_serialize( get_object_vars( $this ) );
	}
}
