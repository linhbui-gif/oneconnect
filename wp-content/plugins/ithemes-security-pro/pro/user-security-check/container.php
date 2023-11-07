<?php

use iThemesSecurity\Strauss\Pimple\Container;

return static function ( Container $c ) {
	$c->extend( 'dashboard.cards', function ( $cards ) {
		require_once __DIR__ . '/cards/abstract-itsec-dashboard-card-security-profile.php';
		require_once __DIR__ . '/cards/class-itsec-dashboard-card-security-profile-list.php';
		require_once __DIR__ . '/cards/class-itsec-dashboard-card-security-profile-pinned.php';

		$cards[] = new ITSEC_Dashboard_Card_Security_Profile_List();
		$cards[] = new ITSEC_Dashboard_Card_Security_Profile_Pinned();

		return $cards;
	} );
};
