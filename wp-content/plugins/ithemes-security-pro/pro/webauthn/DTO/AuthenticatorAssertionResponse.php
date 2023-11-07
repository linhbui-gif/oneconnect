<?php

namespace iThemesSecurity\WebAuthn\DTO;

use iThemesSecurity\Strauss\Assert\Assert;

final class AuthenticatorAssertionResponse extends AuthenticatorResponse {

	/**
	 * This attribute contains the authenticator data returned
	 * by the authenticator.
	 *
	 * @var BinaryString
	 */
	protected $authenticatorData;

	/**
	 * This attribute contains the raw signature returned
	 * from the authenticator
	 *
	 * @var BinaryString
	 */
	protected $signature;

	/**
	 * This attribute contains the user handle returned
	 * from the authenticator, or null if the authenticator
	 * did not return a user handle.
	 *
	 * This matches the {@see PublicKeyCredentialUserEntity::get_id()}
	 * field.
	 *
	 * @var BinaryString|null
	 */
	protected $userHandle;

	public function __construct(
		BinaryString $clientDataJSON,
		BinaryString $authenticatorData,
		BinaryString $signature,
		?BinaryString $userHandle
	) {
		parent::__construct( $clientDataJSON );
		$this->authenticatorData = $authenticatorData;
		$this->signature         = $signature;
		$this->userHandle        = $userHandle;
	}

	public static function hydrate( array $data ): AuthenticatorResponse {
		Assert::that( $data, 'AuthenticatorAssertionResponse hydration does not contain "%s".' )
		      ->keyExists( 'clientDataJSON' )
		      ->keyExists( 'authenticatorData' )
		      ->keyExists( 'signature' )
		      ->keyExists( 'userHandle' );

		return new self(
			BinaryString::from_ascii( $data['clientDataJSON'] ),
			BinaryString::from_ascii( $data['authenticatorData'] ),
			BinaryString::from_ascii( $data['signature'] ),
			! empty( $data['userHandle'] ) ? BinaryString::from_ascii( $data['userHandle'] ) : null
		);
	}

	public function get_authenticator_data(): BinaryString {
		return $this->authenticatorData;
	}

	public function get_signature(): BinaryString {
		return $this->signature;
	}

	public function get_user_handle(): ?BinaryString {
		return $this->userHandle;
	}

	public function jsonSerialize(): array {
		return \ITSEC_Lib::recursively_json_serialize( get_object_vars( $this ) );
	}
}
