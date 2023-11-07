<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class AuthenticatorSelectionCriteria implements \JsonSerializable {

	/**
	 * If this member is present, eligible authenticators are filtered to be
	 * only those authenticators attached with the specified authenticator
	 * attachment modality. The value SHOULD be a member of {@see AuthenticatorAttachment}.
	 *
	 * @var string|null
	 */
	protected $authenticatorAttachment;

	/**
	 * Specifies the extent to which the Relying Party desires to create a
	 * client-side discoverable credential. The value SHOULD be a member of
	 * {@see ResidentKeyRequirement}. If no value is given then the effective
	 * value is required if requireResidentKey is true or discouraged if it is
	 * false or absent.
	 *
	 * @var string
	 */
	protected $residentKey;

	/**
	 * This member is retained for backwards compatibility with WebAuthn Level 1.
	 * Relying Parties SHOULD set it to true only if, residentKey is set to required.
	 *
	 * @var bool
	 */
	protected $requireResidentKey = false;

	/**
	 * This member specifies the Relying Party's requirements regarding user
	 * verification for the create() operation. The value SHOULD be a member
	 * of {@see UserVerificationRequirement}.
	 *
	 * @var string
	 */
	protected $userVerification;

	public function __construct(
		string $authenticatorAttachment = null,
		string $residentKey = ResidentKeyRequirement::DISCOURAGED,
		string $userVerification = UserVerificationRequirement::PREFERRED
	) {
		Assert::that( $authenticatorAttachment )->nullOr()->choice(
			AuthenticatorAttachment::ALL,
			'authenticatorAttachment "%s" is not an element of the valid values: %s'
		);
		Assert::that( $residentKey )->choice(
			ResidentKeyRequirement::ALL,
			'residentKey "%s" is not an element of the valid values: %s'
		);
		Assert::that( $userVerification )->choice(
			UserVerificationRequirement::ALL,
			'userVerification "%s" is not an element of the valid values: %s'
		);

		$this->authenticatorAttachment = $authenticatorAttachment;
		$this->residentKey             = $residentKey;
		$this->userVerification        = $userVerification;

		if ( $residentKey === ResidentKeyRequirement::REQUIRED ) {
			$this->requireResidentKey = true;
		}
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'AuthenticatorSelectionCredential hydration does not contain "%s".' )
		      ->keyExists( 'residentKey' )
		      ->keyExists( 'userVerification' );

		return new self(
			$data['authenticatorAttachment'] ?? null,
			$data['residentKey'],
			$data['userVerification']
		);
	}

	public function get_authenticator_attachment(): string {
		return $this->authenticatorAttachment;
	}

	public function get_resident_key(): string {
		return $this->residentKey;
	}

	public function get_user_verification(): string {
		return $this->userVerification;
	}

	public function jsonSerialize(): array {
		$data = [
			'residentKey'        => $this->residentKey,
			'requireResidentKey' => $this->requireResidentKey,
			'userVerification'   => $this->userVerification,
		];

		if ( $this->authenticatorAttachment ) {
			$data['authenticatorAttachment'] = $this->authenticatorAttachment;
		}

		return $data;
	}
}
