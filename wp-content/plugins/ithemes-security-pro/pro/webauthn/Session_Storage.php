<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Lib\Result;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialCreationOptions;
use iThemesSecurity\WebAuthn\DTO\PublicKeyCredentialRequestOptions;

interface Session_Storage {

	/**
	 * Persists a creation options dictionary.
	 *
	 * @param PublicKeyCredentialCreationOptions $options
	 *
	 * @return Result<string> A result with an identifier.
	 */
	public function persist_creation_options( PublicKeyCredentialCreationOptions $options ): Result;

	/**
	 * Retrieves a creation options dictionary.
	 *
	 * @param string $id An id provided by {@see Session_Storage::persist_creation_options}.
	 *
	 * @return Result<PublicKeyCredentialCreationOptions>
	 */
	public function get_creation_options( string $id ): Result;

	/**
	 * Persists a request options dictionary.
	 *
	 * @param PublicKeyCredentialRequestOptions $options
	 *
	 * @return Result<string> A result with an identifier.
	 */
	public function persist_request_options( PublicKeyCredentialRequestOptions $options ): Result;

	/**
	 * Retrieves a request options dictionary.
	 *
	 * @param string $id An id provided by {@see Session_Storage::persist_request_options()}.
	 *
	 * @return Result<PublicKeyCredentialRequestOptions>
	 */
	public function get_request_options( string $id ): Result;
}
