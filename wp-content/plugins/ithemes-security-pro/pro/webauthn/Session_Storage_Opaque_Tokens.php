<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialCreationOptions;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialRequestOptions;

final class Session_Storage_Opaque_Tokens implements Session_Storage {
	private const CREATION = 'webauthn-session-creation';
	private const REQUEST = 'webauthn-session-request';

	public function persist_creation_options( PublicKeyCredentialCreationOptions $options ): Result {
		$created = \ITSEC_Lib_Opaque_Tokens::create_token( self::CREATION, $options->jsonSerialize() );

		if ( is_wp_error( $created ) ) {
			return Result::error( $created );
		}

		return Result::success( $created );
	}

	public function get_creation_options( string $id ): Result {
		$data = \ITSEC_Lib_Opaque_Tokens::verify_and_get_token_data( self::CREATION, $id );

		if ( is_wp_error( $data ) ) {
			return Result::error( $data );
		}

		try {
			return Result::success( PublicKeyCredentialCreationOptions::hydrate( $data ) );
		} catch ( \Exception $e ) {
			return Result::error( new \WP_Error(
				'itsec.webauthn.session-storage.invalid-hydration-data',
				$e->getMessage()
			) );
		}
	}

	public function persist_request_options( PublicKeyCredentialRequestOptions $options ): Result {
		$created = \ITSEC_Lib_Opaque_Tokens::create_token( self::REQUEST, $options->jsonSerialize() );

		if ( is_wp_error( $created ) ) {
			return Result::error( $created );
		}

		return Result::success( $created );
	}

	public function get_request_options( string $id ): Result {
		$data = \ITSEC_Lib_Opaque_Tokens::verify_and_get_token_data( self::REQUEST, $id );

		if ( is_wp_error( $data ) ) {
			return Result::error( $data );
		}

		try {
			return Result::success( PublicKeyCredentialRequestOptions::hydrate( $data ) );
		} catch ( \Exception $e ) {
			return Result::error( new \WP_Error(
				'itsec.webauthn.session-storage.invalid-hydration-data',
				$e->getMessage()
			) );
		}
	}
}
