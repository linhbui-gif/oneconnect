<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialUserEntity;

interface PublicKeyCredential_Record_Repository {

	/**
	 * Checks if the given Credential ID is available for use.
	 *
	 * @param BinaryString $id
	 *
	 * @return Result<bool> True if available, false if not.
	 */
	public function is_id_available( BinaryString $id ): Result;

	/**
	 * Find a credential by its id.
	 *
	 * @param BinaryString $id
	 *
	 * @return Result<PublicKeyCredential_Record>
	 */
	public function find_by_id( BinaryString $id ): Result;

	/**
	 * Gets the list of credential records for the given user.
	 *
	 * @param PublicKeyCredentialUserEntity $user
	 * @param string                        $status
	 *
	 * @return Result<PublicKeyCredential_Record[]>
	 */
	public function get_credentials_for_user( PublicKeyCredentialUserEntity $user, string $status = PublicKeyCredential_Record::S_ACTIVE ): Result;

	/**
	 * Checks if the user has any stored credentials.
	 *
	 * @param PublicKeyCredentialUserEntity $user
	 *
	 * @return Result<bool>
	 */
	public function user_has_credentials( PublicKeyCredentialUserEntity $user ): Result;

	/**
	 * Saves a credential record.
	 *
	 * @param PublicKeyCredential_Record $record
	 *
	 * @return Result
	 */
	public function persist( PublicKeyCredential_Record $record ): Result;

	/**
	 * Deletes a credential record.
	 *
	 * @param PublicKeyCredential_Record $record
	 *
	 * @return Result
	 */
	public function delete( PublicKeyCredential_Record $record ): Result;

	/**
	 * Deletes any trashed credential records.
	 *
	 * @param int $trash_days Optionally, limit to credentials that have been
	 *                        trahsed for the given number of days.
	 *
	 * @return Result<int>
	 */
	public function delete_trashed_credentials( int $trash_days = 0 ): Result;
}
