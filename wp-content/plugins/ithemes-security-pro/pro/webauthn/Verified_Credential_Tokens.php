<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;

interface Verified_Credential_Tokens {

	public function create_token( PublicKeyCredential_Record $record ): Result;

	public function verify_token( \WP_User $user, string $token ): Result;
}
