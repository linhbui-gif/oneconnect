<?php

use iThemesSecurity\Modules\Password_Expiration\Age_Requirement;
use iThemesSecurity\Modules\Password_Expiration\Force_Requirement;
use iThemesSecurity\User_Groups\Matcher;

return static function ( \iThemesSecurity\Strauss\Pimple\Container $c ) {
	$c[ Age_Requirement::class ] = static function ( \iThemesSecurity\Strauss\Pimple\Container $c ) {
		return new Age_Requirement( $c[ Matcher::class ], ITSEC_Modules::get_config( 'password-expiration' ), 'age' );
	};

	$c[ Force_Requirement::class ] = static function ( \iThemesSecurity\Strauss\Pimple\Container $c ) {
		return new Force_Requirement( ITSEC_Modules::get_config( 'password-expiration' ), 'force' );
	};

	$c['ITSEC_Password_Expiration'] = static function ( \iThemesSecurity\Strauss\Pimple\Container $c ) {
		return new ITSEC_Password_Expiration(
			$c[ Age_Requirement::class ],
			$c[ Force_Requirement::class ]
		);
	};
};
