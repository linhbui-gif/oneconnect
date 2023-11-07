<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialRpEntity;

final class PublicKeyCredentialRpEntity_Factory_Default implements PublicKeyCredentialRpEntity_Factory {
	public function make(): Result {
		$url   = \ITSEC_Lib::get_login_url();
		$parts = wp_parse_url( $url );

		if ( ! empty( $parts['port'] ) ) {
			$id = sprintf( '%s:%d', $parts['host'], $parts['port'] );
		} else {
			$id = $parts['host'];
		}

		$name = trim( get_bloginfo( 'name' ) );

		if ( ! $name ) {
			$name = $parts['host'];
		}

		return Result::success(
			new PublicKeyCredentialRpEntity( $id, $name )
		);
	}
}
