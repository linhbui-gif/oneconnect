<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Contracts\Runnable;

final class Login implements Runnable {
	public function run() {
		add_action( 'itsec_login_interstitial_init', [ $this, 'register_interstitial' ] );
	}

	public function register_interstitial( \ITSEC_Lib_Login_Interstitial $interstitial ): void {
		$interstitial->register(
			Interstitial::SLUG,
			new Interstitial()
		);
	}
}
