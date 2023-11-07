<?php

namespace iThemesSecurity\WebAuthn;

use iThemesSecurity\Contracts\Runnable;

final class Module implements Runnable {

	/** @var Runnable[] */
	private $runnable;

	public function __construct( Runnable ...$runnable ) {
		$this->runnable = $runnable;
	}

	public function run() {
		foreach ( $this->runnable as $runnable ) {
			$runnable->run();
		}

		add_action( 'itsec_scheduled_clear-trashed-passkeys', [ $this, 'clear_trashed_passkeys' ] );
		add_action( 'itsec_passwordless_login_enqueue_profile_scripts', [ $this, 'enqueue_profile' ] );
	}

	public function clear_trashed_passkeys() {
		\ITSEC_Modules::get_container()
		              ->get( PublicKeyCredential_Record_Repository::class )
		              ->delete_trashed_credentials( 7 );
	}

	public function enqueue_profile( \WP_User $user ) {
		if ( $user->ID !== wp_get_current_user()->ID ) {
			return;
		}

		wp_enqueue_style( 'itsec-webauthn-profile' );
		wp_enqueue_script( 'itsec-webauthn-profile' );

		$credentials = rest_do_request( '/ithemes-security/v1/webauthn/credentials' );

		if ( ! $credentials->is_error() ) {
			wp_add_inline_script(
				'itsec-webauthn-profile',
				sprintf(
					"wp.data.dispatch('%s').receiveCredentials( %s );",
					'ithemes-security/webauthn',
					wp_json_encode( $credentials->get_data() )
				)
			);
		}
	}
}
