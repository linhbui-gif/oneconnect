<?php

use iThemesSecurity\Lib\Result;

/**
 * Class ITSEC_Dashboard_Card_Security_Profile
 */
abstract class ITSEC_Dashboard_Card_Security_Profile extends ITSEC_Dashboard_Card {

	/**
	 * Build the UI data for a user.
	 *
	 * @param WP_User $user
	 *
	 * @return array
	 */
	protected function build_user_data( $user ) {
		$last_seen             = ITSEC_Lib_User_Activity::get_instance()->get_last_seen( $user->ID );
		$password_last_changed = ITSEC_Lib_Password_Requirements::password_last_changed( $user );

		$data = array(
			'id'                    => $user->ID,
			'name'                  => ! empty( $user->display_name ) ? $user->display_name : $user->user_login,
			'avatar'                => get_avatar_url( $user ),
			'role'                  => $this->get_role( $user ),
			'two_factor'            => 'not-available',
			'last_active'           => ! $last_seen ? array() : array(
				'time' => ITSEC_Lib::to_rest_date( (int) $last_seen ),
				/* translators: 1. Human Time Diff */
				'diff' => ITSEC_Core::get_current_time_gmt() - HOUR_IN_SECONDS < $last_seen ? sprintf( __( 'Within %s', 'it-l10n-ithemes-security-pro' ), human_time_diff( $last_seen ) ) : sprintf( __( '%s ago', 'it-l10n-ithemes-security-pro' ), human_time_diff( $last_seen ) ),
			),
			'password_strength'     => $this->get_password_strength( $user ),
			'password_last_changed' => array(
				'time' => ITSEC_Lib::to_rest_date( $password_last_changed ),
				/* translators: 1. Human Time Diff */
				'diff' => sprintf( __( '%s old', 'it-l10n-ithemes-security-pro' ), human_time_diff( $password_last_changed ) ),
			),
		);

		/**
		 * Filters the Security Profile data for a user.
		 *
		 * @param array   $data
		 * @param WP_User $user
		 */
		return apply_filters( 'itsec_user_security_profile_data', $data, $user );
	}

	/**
	 * Get the user's role to display.
	 *
	 * @param WP_User $user
	 *
	 * @return string
	 */
	private function get_role( $user ) {
		if ( is_multisite() && is_super_admin( $user->ID ) ) {
			return esc_html__( 'Super Admin', 'it-l10n-ithemes-security-pro' );
		}

		if ( is_multisite() && ! is_user_member_of_blog( $user->ID ) && $site_id = get_user_meta( $user->ID, 'primary_blog', true ) ) {
			$user->for_site( $site_id );
		}

		return implode( ', ', array_map( static function ( $role ) {
			$names = wp_roles()->get_names();

			return isset( $names[ $role ] ) ? translate_user_role( $names[ $role ] ) : $role;
		}, $user->roles ) );
	}

	/**
	 * Get the password strength for a user.
	 *
	 * @param WP_User $user
	 *
	 * @return int
	 */
	private function get_password_strength( $user ) {
		$password_strength = get_user_meta( $user->ID, 'itsec-password-strength', true );

		// If the password strength wasn't retrieved or isn't 0-4, set it to -1 for "Unknown"
		if ( false === $password_strength || '' === $password_strength || ! in_array( $password_strength, range( 0, 4 ) ) ) {
			$password_strength = - 1;
		}

		return (int) $password_strength;
	}

	public function get_links() {
		return [
			[
				'rel'                 => ITSEC_Lib_REST::LINK_REL . 'logout',
				'route'               => 'logout/(?P<user_id>[\d]+)',
				'title'               => __( 'Logout User', 'it-l10n-ithemes-security-pro' ),
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'logout_user' ],
				'cap'                 => 'edit_users',
				'permission_callback' => function ( WP_REST_Request $request ) {
					if ( ! current_user_can( 'edit_user', $request['user_id'] ) ) {
						return new WP_Error( 'rest_cannot_edit_user', __( 'Sorry, you do not have permission to edit this user.', 'it-l10n-ithemes-security-pro' ), [
							'status' => rest_authorization_required_code(),
						] );
					}

					return true;
				}
			],
			[
				'rel'      => ITSEC_Lib_REST::LINK_REL . 'send-2fa-reminder',
				'route'    => 'send-2fa-reminder/(?P<user_id>[\d]+)',
				'title'    => __( 'Send Two-Factor Reminder', 'it-l10n-ithemes-security-pro' ),
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'send_2fa_reminder' ],
				'cap'      => ITSEC_Core::get_required_cap(),
			],
		];
	}

	public function logout_user( $request ) {
		$user = get_userdata( $request['user_id'] );

		if ( ! $user ) {
			return new WP_Error( 'not_found', __( 'User not found.', 'it-l10n-ithemes-security-pro' ), [ 'status' => WP_Http::NOT_FOUND ] );
		}

		$sessions = WP_Session_Tokens::get_instance( $user->ID );

		if ( $user->ID === get_current_user_id() ) {
			$sessions->destroy_others( wp_get_session_token() );

			return Result::success()
			             ->add_success_message( __( 'You are now logged out everywhere else.', 'it-l10n-ithemes-security-pro' ) )
			             ->as_rest_response();
		}

		$sessions->destroy_all();

		return Result::success()
		             ->add_success_message( sprintf( __( '%s has been logged out.', 'it-l10n-ithemes-security-pro' ), $user->display_name ) )
		             ->as_rest_response();
	}

	public function send_2fa_reminder( $request ) {
		$recipient = get_userdata( $request['user_id'] );

		if ( ! $recipient ) {
			return new WP_Error( 'not_found', __( 'User not found.', 'it-l10n-ithemes-security-pro' ), [ 'status' => WP_Http::NOT_FOUND ] );
		}

		$requester = wp_get_current_user();

		ITSEC_Modules::load_module_file( 'utility.php', 'user-security-check' );
		$sent = ITSEC_User_Security_Check_Utility::send_2fa_reminder( $recipient, $requester );

		if ( is_wp_error( $sent ) ) {
			return $sent;
		}

		return Result::success()
		             ->add_success_message( __( 'Reminder E-Mail has been sent.', 'it-l10n-ithemes-security-pro' ) )
		             ->as_rest_response();
	}
}
