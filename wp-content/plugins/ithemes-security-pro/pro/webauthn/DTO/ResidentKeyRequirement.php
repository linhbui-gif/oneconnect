<?php

namespace iThemesSecurity\WebAuthn\DTO;

final class ResidentKeyRequirement {
	const DISCOURAGED = 'discouraged';
	const PREFERRED = 'preferred';
	const REQUIRED = 'required';

	const ALL = [
		self::DISCOURAGED,
		self::PREFERRED,
		self::REQUIRED,
	];
}
