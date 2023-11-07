<?php

use iThemesSecurity\Strauss\Pimple\Container;

return static function ( Container $c ) {
	\ITSEC_Lib::extend_if_able( $c, 'dashboard.cards', function ( $cards ) {
		$cards[] = new ITSEC_Dashboard_Card_Line_Graph( 'fingerprinting', __( 'Trusted Devices', 'it-l10n-ithemes-security-pro' ), [
			[
				'events' => 'fingerprint-status-approved',
				'label'  => __( 'Approved', 'it-l10n-ithemes-security-pro' ),
			],
			[
				'events' => 'fingerprint-status-approved',
				'label'  => __( 'Approved', 'it-l10n-ithemes-security-pro' ),
			],
			[
				'events' => 'fingerprint-status-auto-approved',
				'label'  => __( 'Auto-Approved', 'it-l10n-ithemes-security-pro' ),
			],
			[
				'events' => 'fingerprint-status-denied',
				'label'  => __( 'Blocked', 'it-l10n-ithemes-security-pro' ),
			],
		] );

		return $cards;
	} );
};
