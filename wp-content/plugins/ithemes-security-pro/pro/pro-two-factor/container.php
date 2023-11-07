<?php

namespace iThemesSecurity\Pro_Two_Factor;

use iThemesSecurity\Strauss\Pimple\Container;
use iThemesSecurity\User_Groups;

return static function ( \iThemesSecurity\Strauss\Pimple\Container $c ) {
	$c['module.pro-two-factor.files'] = [
		'active.php' => Module::class,
	];

	$c[ Module::class ] = static function ( Container $c ) {
		return new Module(
			$c[ API::class ],
			\ITSEC_Two_Factor::get_instance(),
			$c[ User_Groups\Matcher::class ]
		);
	};

	$c[ API::class ] = static function ( Container $c ) {
		return new API( $c[ User_Groups\Matcher::class ] );
	};
};
