<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialUserEntity;

final class PublicKeyCredentialUserEntity_Factory_Database implements PublicKeyCredentialUserEntity_Factory {
	const LENGTH = 32;
	const CACHE_GROUP = 'itsec_webauthn_id';

	/** @var \wpdb */
	private $wpdb;

	public function __construct( \wpdb $wpdb ) { $this->wpdb = $wpdb; }

	public function make( \WP_User $user ): Result {
		$cached = wp_cache_get( $user->ID, self::CACHE_GROUP );

		if ( $cached ) {
			$webauthn_id = BinaryString::from_ascii_fast( $cached );
		} else {
			$webauthn_id = $this->fetch_webauthn_id( $user->ID );

			if ( ! $webauthn_id ) {
				if ( ( $generated = $this->generate_webauthn_id( $user->ID ) ) && ! $generated->is_success() ) {
					return $generated;
				}

				$webauthn_id = $generated->get_data();

				if ( ( $saved = $this->save_webauthn_id( $user->ID, $webauthn_id ) ) && ! $saved->is_success() ) {
					return $saved;
				}

				wp_cache_set( $user->ID, $webauthn_id->as_ascii_fast(), self::CACHE_GROUP );
			}
		}

		return Result::success( new PublicKeyCredentialUserEntity(
			$webauthn_id,
			$user->display_name,
			$user->user_login
		) );
	}

	public function find_user_by_id( BinaryString $id ): Result {
		$user_id = (int) $this->wpdb->get_var( $this->wpdb->prepare(
			"SELECT user_id FROM {$this->tn()} WHERE webauthn_id = %s",
			$id->as_ascii_fast()
		) );

		if ( $this->wpdb->last_error ) {
			\ITSEC_Log::add_error( 'webauthn', 'user-entity::read-db-error', [
				'user'  => $id->as_ascii_fast(),
				'error' => $this->wpdb->last_error,
			] );

			return Result::error( new \WP_Error(
				'itsec.webauthn.user-entity.read-db-error',
				Logs::transform_error_code_to_readable_string( 'user-entity', [ 'read-db-error' ] )
			) );
		}

		if ( ! $user_id ) {
			return Result::error( new \WP_Error(
				'itsec.webauthn.user-factory.user-id-not-found',
				__( 'Could not find a user for this passkey.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return Result::error( new \WP_Error(
				'itsec.webauthn.user-factory.user-not-found',
				__( 'Could not find a user for this passkey.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		return Result::success( $user );
	}

	protected function fetch_webauthn_id( int $user_id ): ?BinaryString {
		$encoded = (string) $this->wpdb->get_var( $this->wpdb->prepare(
			"SELECT webauthn_id FROM {$this->tn()} WHERE user_id = %d",
			$user_id
		) );

		if ( ! $encoded ) {
			return null;
		}

		return BinaryString::from_ascii_fast( $encoded );
	}

	/**
	 * Generates a new WebAuthn id for a user.
	 *
	 * @param int $user_id
	 *
	 * @return Result<BinaryString>
	 */
	protected function generate_webauthn_id( int $user_id ): Result {
		try {
			$bytes = random_bytes( self::LENGTH );
		} catch ( \Exception $e ) {
			\ITSEC_Log::add_fatal_error( 'webauthn', 'user-entity::no-random-bytes', [
				'user'      => $user_id,
				'exception' => $e->getMessage(),
			] );

			return Result::error( new \WP_Error(
				'itsec.webauthn.user-factory.no-random-bytes',
				Logs::transform_error_code_to_readable_string( 'user-entity', [ 'no-random-bytes' ] )
			) );
		}

		return Result::success( new BinaryString( $bytes ) );
	}

	protected function save_webauthn_id( int $user_id, BinaryString $webauthn_id ): Result {
		$inserted = $this->wpdb->insert( $this->tn(), [
			'user_id'     => $user_id,
			'webauthn_id' => $webauthn_id->as_ascii_fast(),
		] );

		if ( ! $inserted ) {
			\ITSEC_Log::add_error( 'webauthn', 'user-entity::write-db-error', [
				'user'  => $user_id,
				'error' => $this->wpdb->last_error,
			] );

			return Result::error( new \WP_Error(
				'itsec.webauthn.user-entity.write-db-error',
				Logs::transform_error_code_to_readable_string( 'user-entity', [ 'write-db-error' ] )
			) );
		}

		return Result::success();
	}

	private function tn(): string {
		return $this->wpdb->base_prefix . 'itsec_webauthn_users';
	}
}
