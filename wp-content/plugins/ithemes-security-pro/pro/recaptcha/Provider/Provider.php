<?php

namespace iThemesSecurity\Recaptcha\Provider;

use iThemesSecurity\Lib\Result;

interface Provider {

	/**
	 * Checks if all necessary settings have been configured
	 * in order to use the CAPTCHA provider.
	 *
	 * @return bool
	 */
	public function is_configured(): bool;

	/**
	 * Gets the HTML to output the CAPTCHA challenge.
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_html( array $args ): string;

	/**
	 * Gets the text used in the GDPR Opt-In notice.
	 *
	 * @return string
	 */
	public function get_opt_in_text(): string;

	/**
	 * Registers the JS to run the CAPTCHA challenge.
	 *
	 * This MUST NOT enqueue or depend on the SDK.
	 *
	 * @return void
	 */
	public function register_assets(): void;

	/**
	 * Gets the url to the external JS for the CAPTCHA API Provider.
	 *
	 * @param bool $load_immediately Whether to immediately call the CAPTCHA API,
	 *                               or to only load the SDK and let the controlling
	 *                               code initialize the challenge.
	 *
	 * @return string
	 */
	public function get_sdk_url( bool $load_immediately ): string;

	/**
	 * Gets the name of the JavaScript function used to load the CAPTCHA challenge.
	 *
	 * @return string
	 */
	public function get_js_load_function(): string;

	/**
	 * Validates that the provided CAPTCHA code is acceptable.
	 *
	 * The following error codes have special meaning:
	 *
	 * - itsec.recaptcha.incorrect: Used when the user doesn't pass the CAPTCHA test.
	 * - itsec.recaptcha.form-not-submitted: Used when there is no CAPTCHA code in the request.
	 * - itsec.recaptcha.http-error: Used when the CAPTCHA API cannot be contacted.
	 * - itsec.recaptcha.invalid-response: Used when the CAPTCHA API returns an invalid response.
	 * - itsec.recaptcha.invalid-api-keys: Used when the CAPTCHA API keys are invalid.
	 *
	 * @param array $args
	 *
	 * @return Result<true>
	 */
	public function validate( array $args ): Result;
}
