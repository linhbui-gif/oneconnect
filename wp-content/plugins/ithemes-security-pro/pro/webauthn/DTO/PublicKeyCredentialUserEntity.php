<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class PublicKeyCredentialUserEntity extends PublicKeyCredentialEntity {

	/**
	 * A human-palatable identifier for a user account. It is intended only for display,
	 * aiding the user in determining the difference between user accounts with similar displayNames.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The user handle of the user account. A user handle is an opaque byte sequence
	 * with a maximum size of 64 bytes, and is not meant to be displayed to the user.
	 *
	 * @var BinaryString
	 */
	protected $id;

	/**
	 * A human-palatable name for the user account, intended only for display.
	 *
	 * @var string
	 */
	protected $displayName;

	public function __construct( BinaryString $id, string $displayName, string $name ) {
		Assert::that( $displayName )->notBlank( 'displayName "%s" is blank, but was expected to contain a value.' );

		$this->id          = $id;
		$this->displayName = $displayName;
		parent::__construct( $name );
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'PublicKeyCredentialUserEntity hydration does not contain "%s".' )
		      ->keyExists( 'id' )
		      ->keyExists( 'displayName' )
		      ->keyExists( 'name' );

		return new self(
			BinaryString::from_ascii_fast( $data['id'] ),
			$data['displayName'],
			$data['name']
		);
	}

	public function get_id(): BinaryString {
		return $this->id;
	}

	public function get_display_name(): string {
		return $this->displayName;
	}

	public function jsonSerialize(): array {
		return \ITSEC_Lib::recursively_json_serialize( get_object_vars( $this ) );
	}
}
