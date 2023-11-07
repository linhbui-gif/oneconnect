<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

/**
 * The PublicKeyCredentialEntity dictionary describes a user account, or a WebAuthn Relying Party,
 * which a public key credential is associated with or scoped to, respectively.
 */
abstract class PublicKeyCredentialEntity implements \JsonSerializable {

	/**
	 * A human-palatable name for the entity.
	 *
	 * @var string
	 */
	protected $name;

	public function __construct( string $name ) {
		Assert::that( $name )->notEmpty();
		$this->name = $name;
	}

	public function get_name(): string {
		return $this->name;
	}
}
