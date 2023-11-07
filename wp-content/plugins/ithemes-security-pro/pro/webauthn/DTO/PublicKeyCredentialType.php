<?php

namespace iThemesSecurity\WebAuthn\DTO;

final class PublicKeyCredentialType {
	const PUBLIC_KEY = 'public-key';

	const ALL = [
		self::PUBLIC_KEY,
	];
}
