<?php

namespace iThemesSecurity\WebAuthn;

use ITSEC_Login_Interstitial_Session;
use WP_User;

final class Interstitial extends \ITSEC_Login_Interstitial {

	private const LAST_PROMPT_META = '_itsec_webauthn_last_prompt';
	private const SKIP_TIMES_META = '_itsec_webauthn_skips';

	const SLUG = 'webauthn';

	public function show_on_wp_login_only( WP_User $user ) {
		return true;
	}

	public function show_after_authentication(): bool {
		return true;
	}

	public function show_to_user( WP_User $user, $is_requested ) {
		if ( ! is_ssl() ) {
			return false;
		}

		if ( ! in_array( 'webauthn', \ITSEC_Passwordless_Login_Utilities::get_available_methods(), true ) ) {
			return false;
		}

		if ( ! \ITSEC_Passwordless_Login_Utilities::is_available_for_user( $user ) ) {
			return false;
		}

		if ( $is_requested ) {
			return true;
		}

		if ( in_array( 'webauthn', \ITSEC_Passwordless_Login_Utilities::get_enabled_methods_for_user( $user ), true ) ) {
			return false;
		}

		$last_prompt = (int) get_user_meta( $user->ID, self::LAST_PROMPT_META, true );

		if ( ! \ITSEC_Passwordless_Login_Utilities::is_enabled_for_user( $user ) ) {
			$show = false;
		} else {
			$time_elapsed = \ITSEC_Core::get_current_time_gmt() - $last_prompt;
			$show         = $time_elapsed / WEEK_IN_SECONDS > 2;
		}

		/**
		 * Filters whether a user should be shown the WebAuthn setup interstitial.
		 *
		 * This cannot be used to enable the interstitial if the requirements are not met.
		 * For example, SSL is enabled and the WebAuthn method is available.
		 *
		 * @param bool    $show
		 * @param WP_User $user
		 * @param int     $last_prompt The time the user was last shown the interstitial as a UNIX epoch.
		 */
		return apply_filters( 'itsec_webauthn_show_setup_interstitial', $show, $user, $last_prompt );
	}

	public function pre_render( ITSEC_Login_Interstitial_Session $session ) {
		add_action( 'login_enqueue_scripts', function () {
			$credentials = rest_do_request( '/ithemes-security/v1/webauthn/credentials' );
			wp_enqueue_style( 'itsec-webauthn-login' );
			wp_enqueue_script( 'itsec-webauthn-login' );

			if ( ! $credentials->is_error() ) {
				wp_add_inline_script(
					'itsec-webauthn-login',
					sprintf(
						"wp.data.dispatch('%s').receiveCredentials( %s );",
						'ithemes-security/webauthn',
						wp_json_encode( $credentials->get_data() )
					)
				);
			}
		} );
	}

	public function render( ITSEC_Login_Interstitial_Session $session, array $args ) {
		printf( '<div id="itsec-webauthn-login" data-is-requested="%d"></div>', $session->is_current_requested() );
	}

	public function has_submit() {
		return true;
	}

	public function submit( ITSEC_Login_Interstitial_Session $session, array $data ) {
		$user = $session->get_user();

		if ( ! empty( $data['itsec_skip'] ) ) {
			update_user_meta( $user->ID, self::LAST_PROMPT_META, \ITSEC_Core::get_current_time_gmt() );

			$skips = (int) get_user_meta( $user->ID, self::SKIP_TIMES_META, true );
			update_user_meta( $user->ID, self::SKIP_TIMES_META, $skips + 1 );
		}

		if ( $session->is_interstitial_requested( self::SLUG ) && is_user_logged_in() ) {
			$redirect = $session->get_redirect_to() ?: admin_url();

			wp_safe_redirect( $redirect );
			die;
		}

		return null;
	}

	public function is_completion_forced( ITSEC_Login_Interstitial_Session $session ) {
		return false;
	}

	public function get_priority() {
		return 10;
	}
}
