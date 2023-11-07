<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\BinaryString;

final class Verified_Credential_Opaque_Tokens implements Verified_Credential_Tokens {

	private const TYPE = 'webauthn-verified';
	private const TTL = 3 * 60;

	/** @var PublicKeyCredentialUserEntity_Factory */
	private $user_factory;

	public function __construct( PublicKeyCredentialUserEntity_Factory $user_factory ) { $this->user_factory = $user_factory; }

	public function create_token( PublicKeyCredential_Record $record ): Result {
		$token = \ITSEC_Lib_Opaque_Tokens::create_token( self::TYPE, [
			'cid' => $record->get_id()->as_ascii_fast(),
			'uid' => $record->get_user()->as_ascii_fast()
		] );

		if ( is_wp_error( $token ) ) {
			return Result::error( $token );
		}

		return Result::success( $token );
	}

	public function verify_token( \WP_User $user, string $token ): Result {
		$verified = \ITSEC_Lib_Opaque_Tokens::verify_and_get_token_data( self::TYPE, $token, self::TTL );

		if ( is_wp_error( $verified ) ) {
			return Result::error( $verified );
		}

		$entity = $this->user_factory->make( $user );

		if ( ! $entity->is_success() ) {
			return $entity;
		}

		if ( ! $entity->get_data()->get_id()->equals( BinaryString::from_ascii_fast( $verified['uid'] ) ) ) {
			return Result::error( new \WP_Error(
				'itsec.webauthn.verified-credential-tokens.invalid-user',
				__( 'This token is for an invalid user.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		\ITSEC_Lib_Opaque_Tokens::delete_token( $token );

		return Result::success();
	}
}
