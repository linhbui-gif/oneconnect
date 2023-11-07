<?php

namespace iThemesSecurity\Pro_Two_Factor;

use iThemesSecurity\Contracts\Runnable;
use iThemesSecurity\User_Groups;
use ITSEC_Modules;

class Module implements Runnable {

	/** @var API */
	private $api;

	/** @var \ITSEC_Two_Factor */
	private $two_factor;

	/** @var User_Groups\Matcher */
	private $matcher;

	/**
	 * Module constructor.
	 *
	 * @param API                 $api
	 * @param \ITSEC_Two_Factor   $two_factor
	 * @param User_Groups\Matcher $matcher
	 */
	public function __construct( API $api, \ITSEC_Two_Factor $two_factor, User_Groups\Matcher $matcher ) {
		$this->api        = $api;
		$this->two_factor = $two_factor;
		$this->matcher    = $matcher;
	}

	public function run() {
		add_filter( 'itsec_two_factor_requirement_reason', [ $this, 'requirement_reason' ], 10, 2 );
		add_filter( 'itsec_two_factor_requirement_reason_description', [ $this, 'requirement_reason_description' ], 10, 2 );
		add_action( 'itsec_two_factor_interstitial_after_auth_page', [ $this, 'show_remember_me' ], 10, 2 );
		add_filter( 'itsec_two_factor_interstitial_show_to_user', [ $this, 'two_factor_remember_me_bypass' ], 10, 2 );
		add_action( 'itsec-two-factor-successful-authentication', [ $this, 'set_remember_me_cookie' ], 10, 3 );
		add_action( 'updated_post_meta', array( $this, 'clear_remember_on_password_change' ), 10, 3 );
	}

	public function requirement_reason( $reason, $user ) {
		if ( $reason ) {
			return $reason;
		}

		$settings = ITSEC_Modules::get_settings( 'two-factor' );

		if ( $this->matcher->matches( User_Groups\Match_Target::for_user( $user ), $settings['protect_user_group'] ) ) {
			return 'user_type';
		}

		if ( $settings['protect_vulnerable_users'] && ! $this->two_factor->is_user_excluded( $user ) ) {
			$password_strength = get_user_meta( $user->ID, 'itsec-password-strength', true );

			if ( ( is_string( $password_strength ) || is_int( $password_strength ) ) && $password_strength >= 0 && $password_strength <= 2 ) {
				return 'vulnerable_users';
			}
		}

		if ( $settings['protect_vulnerable_site'] && ITSEC_Modules::is_active( 'version-management' ) && ! $this->two_factor->is_user_excluded( $user ) ) {
			$version_management_settings = ITSEC_Modules::get_settings( 'version-management' );

			if ( $version_management_settings['is_software_outdated'] ) {
				return 'vulnerable_site';
			}
		}

		return null;
	}

	public function requirement_reason_description( $reason, $description ) {
		switch ( $reason ) {
			case 'user_type':
				return esc_html__( 'Your user requires two-factor in order to log in.', 'it-l10n-ithemes-security-pro' );
			case 'vulnerable_users':
				return esc_html__( 'The site requires any user with a weak password to use two-factor in order to log in.', 'it-l10n-ithemes-security-pro' );
			case 'vulnerable_site':
				return esc_html__( 'This site requires two-factor in order to log in.', 'it-l10n-ithemes-security-pro' );
			default:
				return $description;
		}
	}

	public function show_remember_me( $provider, \ITSEC_Login_Interstitial_Session $session ) {
		if ( ! $this->api->is_remember_allowed( $session->get_user() ) ) {
			return;
		}

		?>
		<p style="margin-bottom: -1em">
			<label for="itsec-remember-2fa" style="font-size: 12px">
				<input type="checkbox" name="itsec_remember_2fa" id="itsec-remember-2fa">
				<?php esc_html_e( 'Remember Device for 30 Days', 'it-l10n-ithemes-security-pro' ); ?>
			</label>
		</p>
		<?php
	}

	public function two_factor_remember_me_bypass( $show_to_user, $user ) {
		if ( ! $show_to_user || empty( $_COOKIE[ API::REMEMBER_COOKIE ] ) || ! $this->api->is_remember_allowed( $user ) ) {
			return $show_to_user;
		}

		$token = $_COOKIE[ API::REMEMBER_COOKIE ];
		$valid = $this->api->validate_remember_token( $user, $token );

		if ( ! $valid ) {
			$this->api->clear_remember_cookie();

			return $show_to_user;
		}

		$this->api->set_remember_cookie( $user );

		return false;
	}

	public function set_remember_me_cookie( $user_id, $provider, $post_data ) {
		$user = get_userdata( $user_id );

		if ( $user && ! empty( $post_data['itsec_remember_2fa'] ) && $this->api->is_remember_allowed( $user ) ) {
			$this->api->set_remember_cookie( $user );
		}
	}

	/**
	 * When a user's password is updated, clear any remember me meta keys.
	 *
	 * @param int    $meta_id
	 * @param int    $user_id
	 * @param string $meta_key
	 */
	public function clear_remember_on_password_change( $meta_id, $user_id, $meta_key ) {
		if ( 'itsec_last_password_change' === $meta_key && $user = get_userdata( $user_id ) ) {
			$this->api->delete_remember_token( $user );
		}
	}
}
