<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\DTO\PublicKey;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialUserEntity;

final class PublicKeyCredential_Record_Repository_Database implements PublicKeyCredential_Record_Repository {

	/** @var \wpdb */
	protected $wpdb;

	public function __construct( \wpdb $wpdb ) { $this->wpdb = $wpdb; }

	public function is_id_available( BinaryString $id ): Result {
		$found = $this->wpdb->get_var(
			$this->wpdb->prepare( "SELECT id FROM {$this->tn()} WHERE ref = %s", $this->generate_ref( $id ) )
		);

		if ( $this->wpdb->last_error ) {
			return $this->db_error( 'id-available-failed', [ 'id' => $id->as_ascii_fast() ] );
		}

		return Result::success( ! $found );
	}

	public function find_by_id( BinaryString $id ): Result {
		$data = $this->wpdb->get_row(
			$this->wpdb->prepare( "SELECT * FROM {$this->tn()} WHERE ref = %s", $this->generate_ref( $id ) ),
			ARRAY_A
		);

		if ( $this->wpdb->last_error ) {
			return $this->db_error( 'id-find-failed', [ 'id' => $id->as_ascii_fast() ] );
		}

		if ( ! $data ) {
			return Result::error( new \WP_Error(
				'itsec.webauthn.credential-record-repository.not-found',
				__( 'No WebAuthn credential was found with that id.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::NOT_FOUND ]
			) );
		}

		$record = $this->hydrate( $data );

		if ( ! $record ) {
			return Result::error( new \WP_Error(
				'itsec.webauthn.credential-record-repository.invalid-record',
				__( 'WebAuthn credential is invalid.', 'it-l10n-ithemes-security-pro' )
			) );
		}

		// There is an infinitesimal chance that there is a hash conflict,
		// check if the record's true id matches the requested id.
		if ( ! $record->get_id()->equals( $id ) ) {
			\ITSEC_Log::add_error( 'webauthn', 'hash-conflict', [
				'id' => $id->as_ascii_fast(),
			] );

			return Result::error( new \WP_Error(
				'itsec.webauthn.credential-record-repository.not-found',
				__( 'No WebAuthn credential was found with that id.', 'it-l10n-ithemes-security-pro' ),
				[ 'status' => \WP_Http::NOT_FOUND ]
			) );
		}

		return Result::success( $record );
	}

	public function user_has_credentials( PublicKeyCredentialUserEntity $user ): Result {
		$has_credentials = (bool) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT id FROM {$this->tn()} WHERE webauthn_user = %s LIMIT 1",
				$user->get_id()->as_ascii_fast()
			)
		);

		if ( $this->wpdb->last_error ) {
			return $this->db_error( 'user-has-credentials-failed', [
				'user' => $user->get_id()->as_ascii_fast(),
			] );
		}

		return Result::success( $has_credentials );
	}

	public function get_credentials_for_user( PublicKeyCredentialUserEntity $user, string $status = PublicKeyCredential_Record::S_ACTIVE ): Result {
		$where   = [ 'webauthn_user = %s' ];
		$prepare = [ $user->get_id()->as_ascii_fast() ];

		if ( $status ) {
			$where[]   = 'status = %s';
			$prepare[] = $status;
		}

		$where_stmt = implode( ' AND ', $where );

		$rows = $this->wpdb->get_results( $this->wpdb->prepare(
			"SELECT * FROM {$this->tn()} WHERE {$where_stmt} ORDER BY created_at ASC",
			$prepare
		), ARRAY_A );

		if ( $this->wpdb->last_error ) {
			return $this->db_error( 'get-user-credentials-failed', [
				'user' => $user->get_id()->as_ascii_fast(),
			] );
		}

		$records = array_map( [ $this, 'hydrate' ], $rows );

		return Result::success( array_filter( $records ) );
	}

	public function persist( PublicKeyCredential_Record $record ): Result {
		$created = $this->wpdb->replace( $this->tn(), [
			'ref'             => $this->generate_ref( $record->get_id() ),
			'id'              => $record->get_id()->as_ascii_fast(),
			'type'            => $record->get_type(),
			'transports'      => wp_json_encode( $record->get_transports() ),
			'public_key'      => wp_json_encode( $record->get_public_key() ),
			'sign_count'      => $record->get_signature_count(),
			'backup_eligible' => $record->is_eligible_for_backups(),
			'backed_up'       => $record->is_backed_up(),
			'webauthn_user'   => $record->get_user()->as_ascii_fast(),
			'created_at'      => $record->get_created_at()->format( 'Y-m-d H:i:s' ),
			'last_used'       => $record->get_last_used() ? $record->get_last_used()->format( 'Y-m-d H:i:s' ) : null,
			'trashed_at'      => $record->get_trashed_at() ? $record->get_trashed_at()->format( 'Y-m-d H:i:s' ) : null,
			'label'           => $record->get_label(),
			'status'          => $record->get_status(),
		] );

		if ( ! $created ) {
			return $this->db_error( __( 'Could not persist WebAuthn credential.', 'it-l10n-ithemes-security-pro' ), [
				'credential' => $record->get_id()->as_ascii_fast(),
				'user'       => $record->get_user()->as_ascii_fast(),
			] );
		}

		return Result::success( true );
	}

	public function delete( PublicKeyCredential_Record $record ): Result {
		$this->wpdb->delete( $this->tn(), [
			'ref' => $this->generate_ref( $record->get_id() ),
		] );

		if ( $this->wpdb->last_error ) {
			return $this->db_error( __( 'Could not delete WebAuthn credential.', 'it-l10n-ithemes-security-pro' ), [
				'credential' => $record->get_id()->as_ascii_fast(),
				'user'       => $record->get_user()->as_ascii_fast(),
			] );
		}

		return Result::success( true );
	}

	public function delete_trashed_credentials( int $trash_days = 0 ): Result {
		$query   = "DELETE FROM {$this->tn()} WHERE `status` = %s";
		$prepare = [ PublicKeyCredential_Record::S_TRASH ];

		if ( $trash_days ) {
			$query     .= ' AND `trashed_at` < %s';
			$prepare[] = gmdate( 'Y-m-d H:i:s', time() - ( DAY_IN_SECONDS * $trash_days ) );
		}

		$sql   = $this->wpdb->prepare( $query, $prepare );
		$count = $this->wpdb->query( $sql );

		if ( $this->wpdb->last_error ) {
			return $this->db_error( 'delete-trashed-failed', [] );
		}

		return Result::success( $count );
	}

	private function hydrate( array $data ): ?PublicKeyCredential_Record {
		$transports = json_decode( $data['transports'], true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return null;
		}

		$public_key = json_decode( $data['public_key'], true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return null;
		}

		try {
			$utc        = new \DateTimeZone( 'UTC' );
			$created_at = new \DateTimeImmutable( $data['created_at'], $utc );
			$last_used  = $data['last_used'] ? new \DateTimeImmutable( $data['last_used'], $utc ) : null;
			$trashed_at = $data['trashed_at'] ? new \DateTimeImmutable( $data['trashed_at'], $utc ) : null;

			return new PublicKeyCredential_Record(
				BinaryString::from_ascii_fast( $data['id'] ),
				$data['type'],
				$transports,
				PublicKey::hydrate( $public_key ),
				$data['sign_count'],
				(bool) $data['backup_eligible'],
				(bool) $data['backed_up'],
				BinaryString::from_ascii_fast( $data['webauthn_user'] ),
				$created_at,
				$data['label'],
				$data['status'],
				$last_used,
				$trashed_at
			);
		} catch ( \Exception $e ) {
			return null;
		}
	}

	private function tn(): string {
		return $this->wpdb->base_prefix . 'itsec_webauthn_credentials';
	}

	private function db_error( string $code, array $args ): Result {
		\ITSEC_Log::add_error( 'webauthn', "credential-repository::{$code}", array_merge( $args, [
			'error' => $this->wpdb->last_error,
		] ) );

		return Result::error( new \WP_Error(
			'itsec.webauthn.credential-record-repository.db-error',
			Logs::transform_error_code_to_readable_string( 'credential-repository', [ $code ] )
		) );
	}

	private function generate_ref( BinaryString $id ): string {
		return hash( 'sha256', $id->as_ascii_fast() );
	}
}
