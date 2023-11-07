<?php

namespace iThemesSecurity\WebAuthn\DTO;

final class AuthenticatorAttachment {
	const PLATFORM = 'platform';
	const CROSS_PLATFORM = 'cross-platform';

	const ALL = [
		self::PLATFORM,
		self::CROSS_PLATFORM,
	];
}
