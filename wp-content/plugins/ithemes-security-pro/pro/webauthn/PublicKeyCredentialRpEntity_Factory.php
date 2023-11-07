<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialRpEntity;

interface PublicKeyCredentialRpEntity_Factory {

	/**
	 * Makes a Relying Party entity for the current site.
	 *
	 * @return Result<PublicKeyCredentialRpEntity>
	 */
	public function make(): Result;
}
