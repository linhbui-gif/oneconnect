<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialRequestOptions;

interface PublicKeyCredentialRequestOptions_Factory {

	/**
	 * Makes a Request Options dictionary for the given user.
	 *
	 * @param \WP_User|null $user Optionally, identify the user who is attempting to log in.
	 *
	 * @return Result<PublicKeyCredentialRequestOptions>
	 */
	public function make( ?\WP_User $user ): Result;
}
