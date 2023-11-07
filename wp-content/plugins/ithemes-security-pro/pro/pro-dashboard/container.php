<?php

namespace iThemesSecurity\Pro_Dashboard;

return static function ( \iThemesSecurity\Strauss\Pimple\Container $c ) {
	$c['module.pro-dashboard.files'] = [
		'rest.php' => REST::class,
	];

	$c[ REST::class ] = static function () {
		return new REST();
	};
};
