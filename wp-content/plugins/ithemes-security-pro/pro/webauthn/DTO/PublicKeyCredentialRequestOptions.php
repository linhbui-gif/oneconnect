<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class PublicKeyCredentialRequestOptions implements \JsonSerializable {

	/**
	 * This member specifies a challenge that the authenticator signs,
	 * along with other data, when producing an authentication assertion.
	 *
	 * @var BinaryString
	 */
	protected $challenge;

	/**
	 * This OPTIONAL member specifies the RP ID claimed by the Relying Party.
	 * The client MUST verify that the Relying Party's origin matches the
	 * scope of this RP ID.
	 *
	 * @var string
	 */
	protected $rpId;

	/**
	 * This OPTIONAL member is used by the client to find authenticators
	 * eligible for this authentication ceremony. The list is ordered in
	 * descending order of preference: the first item in the list is the
	 * most preferred credential, and the last is the least preferred.
	 *
	 * It can be used in two ways:
	 *
	 * If the user account to authenticate is already identified
	 * (e.g., if the user has entered a username), then the Relying Party
	 * SHOULD use this member to list the credentials registered to the
	 * user account. This SHOULD usually include all the user account’s credentials.
	 *
	 * If the user account to authenticate is not already identified, then the
	 * Relying Party MAY leave this member empty or unspecified. In this case,
	 * only discoverable credentials will be utilized in this authentication
	 * ceremony, and the user account MAY be identified by the userHandle of
	 * the resulting AuthenticatorAssertionResponse.
	 *
	 * Note: Older implementations require allowCredentials to be present.
	 * For instance Yubikeys and Google Chrome.
	 *
	 * @var PublicKeyCredentialDescriptor[]
	 */
	protected $allowCredentials;

	/**
	 * This OPTIONAL member specifies the Relying Party's requirements regarding
	 * user verification for the get() operation. The value SHOULD be a member
	 * of {@see UserVerificationRequirement}.
	 *
	 * @var string
	 */
	protected $userVerification;

	public function __construct(
		BinaryString $challenge,
		string $rpId,
		array $allowCredentials = [],
		string $userVerification = UserVerificationRequirement::PREFERRED
	) {
		Assert::that( $userVerification )->choice(
			UserVerificationRequirement::ALL,
			'userVerification "%s" is not an element of the valid values: %s'
		);

		$this->challenge        = $challenge;
		$this->rpId             = $rpId;
		$this->allowCredentials = $allowCredentials;
		$this->userVerification = $userVerification;
	}

	public static function hydrate( array $data ): self {
		Assert::that( $data, 'PublicKeyCredentialRequestOptions hydration does not contain "%s".' )
		      ->keyExists( 'challenge' )
		      ->keyExists( 'rpId' )
		      ->keyExists( 'allowCredentials' )
		      ->keyExists( 'userVerification' );
		Assert::that( $data['allowCredentials'] )
		      ->isArray( 'allowCredentials "%s" is not an array.' );
		Assert::thatAll( $data['allowCredentials'] )
		      ->isArray( 'allowCredentials item "%s" is not an array.' );

		return new self(
			BinaryString::from_ascii( $data['challenge'] ),
			$data['rpId'],
			array_map(
				[ PublicKeyCredentialDescriptor::class, 'hydrate' ],
				$data['allowCredentials']
			),
			$data['userVerification']
		);
	}

	public function get_challenge(): BinaryString {
		return $this->challenge;
	}

	public function get_rp_id(): string {
		return $this->rpId;
	}

	/** @return PublicKeyCredentialDescriptor[] */
	public function get_allowed_credentials(): array {
		return $this->allowCredentials;
	}

	public function get_user_verification(): string {
		return $this->userVerification;
	}

	public function jsonSerialize(): array {
		return \ITSEC_Lib::recursively_json_serialize( get_object_vars( $this ) );
	}
}
