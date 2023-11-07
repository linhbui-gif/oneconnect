<?php

namespace iThemesSecurity\Pro_Two_Factor;

use iThemesSecurity\User_Groups;
use ITSEC_Core;
use ITSEC_Lib;
use ITSEC_Lib_Fingerprinting;
use ITSEC_Log;
use ITSEC_Modules;
use WP_User;

final class API {
	const REMEMBER_COOKIE = 'itsec_remember_2fa';
	const REMEMBER_META_KEY = '_itsec_remember_2fa';

	/** @var User_Groups\Matcher */
	private $matcher;

	/**
	 * Module constructor.
	 *
	 * @param User_Groups\Matcher $matcher
	 */
	public function __construct( User_Groups\Matcher $matcher ) {
		$this->matcher = $matcher;
	}

	/**
	 * Validates that a remember me token is correct.
	 *
	 * @param WP_User $user
	 * @param string  $token
	 *
	 * @return bool
	 */
	public function validate_remember_token( $user, $token ): bool {
		foreach ( get_user_meta( $user->ID, self::REMEMBER_META_KEY ) as $possible ) {
			if ( empty( $possible['hashed'] ) || empty( $possible['expires'] ) || $possible['expires'] < ITSEC_Core::get_current_time_gmt() ) {
				delete_user_meta( $user->ID, self::REMEMBER_META_KEY, $possible );

				continue;
			}

			if ( ! ITSEC_Lib::verify_token( $token, $possible['hashed'] ) ) {
				continue;
			}

			$match = ITSEC_Lib_Fingerprinting::check_global_state_fingerprint_for_match( $user );

			if ( ! $match || $match->get_match_percent() < 85 ) {
				ITSEC_Log::add_debug( 'two_factor', 'remember_fingerprint_failed', array( 'match' => $match ), array( 'user_id' => $user->ID ) );

				return false;
			}

			ITSEC_Log::add_debug( 'two_factor', 'remember_success', $possible, array( 'user_id' => $user->ID ) );

			delete_user_meta( $user->ID, self::REMEMBER_META_KEY, $possible );

			return true;
		}

		ITSEC_Log::add_debug( 'two_factor', 'remember_failed', false, array( 'user_id' => $user->ID ) );

		return false;
	}

	/**
	 * Set the remember 2fa cookie.
	 *
	 * @param WP_User $user
	 *
	 * @return bool
	 */
	public function set_remember_cookie( $user ) {
		if ( ! $token = ITSEC_Lib::generate_token() ) {
			return false;
		}

		if ( ! $hashed = ITSEC_Lib::hash_token( $token ) ) {
			return false;
		}

		$expires = ITSEC_Core::get_current_time_gmt() + MONTH_IN_SECONDS;

		if ( ! add_user_meta( $user->ID, self::REMEMBER_META_KEY, $data = compact( 'hashed', 'expires' ) ) ) {
			return false;
		}

		ITSEC_Log::add_debug( 'two_factor', 'remember_generated', $data, array( 'user_id' => $user->ID ) );

		return setcookie( self::REMEMBER_COOKIE, $token, $expires, ITSEC_Lib::get_home_root(), COOKIE_DOMAIN, is_ssl(), true );
	}

	/**
	 * Clear the remember 2fa cookie.
	 *
	 * @return bool
	 */
	public function clear_remember_cookie() {
		return setcookie( self::REMEMBER_COOKIE, ' ', ITSEC_Core::get_current_time_gmt() - YEAR_IN_SECONDS, ITSEC_Lib::get_home_root(), COOKIE_DOMAIN, is_ssl(), true );
	}

	/**
	 * Is the user allowed to remember 2fa.
	 *
	 * @param WP_User $user
	 *
	 * @return bool
	 */
	public function is_remember_allowed( $user ) {
		$target = User_Groups\Match_Target::for_user( $user );

		if ( ! $this->matcher->matches( $target, ITSEC_Modules::get_setting( 'two-factor', 'remember_group' ) ) ) {
			return false;
		}

		if ( ! \ITSEC_Lib_Fingerprinting::applies_to_user( $user ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Deletes the 2fa remember token for a user.
	 *
	 * @param WP_User $user
	 */
	public function delete_remember_token( $user ) {
		delete_user_meta( $user->ID, self::REMEMBER_META_KEY );
	}
}
