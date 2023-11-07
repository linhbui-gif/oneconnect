<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\BinaryString;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialUserEntity;

interface PublicKeyCredentialUserEntity_Factory {

	/**
	 * Make a User Entity object for the requested WordPress user.
	 *
	 * @param \WP_User $user The WordPress user object.
	 *
	 * @return Result<PublicKeyCredentialUserEntity>
	 */
	public function make( \WP_User $user ): Result;

	/**
	 * Finds a WordPress user by their User Entity id.
	 *
	 * @param BinaryString $id  User handle.
	 *
	 * @return Result<\WP_User> Successful result with the found WP_User,
	 *                          or an error result if no corresponding user
	 *                          is found.
	 */
	public function find_user_by_id( BinaryString $id ): Result;
}
