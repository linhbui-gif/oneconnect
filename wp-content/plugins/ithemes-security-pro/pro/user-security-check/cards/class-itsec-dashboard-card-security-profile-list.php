<?php

use iThemesSecurity\Lib\Result;

/**
 * Class ITSEC_Dashboard_Card_Security_Profile_List
 */
class ITSEC_Dashboard_Card_Security_Profile_List extends ITSEC_Dashboard_Card_Security_Profile {

	/**
	 * @inheritDoc
	 */
	public function get_slug() {
		return 'security-profile-list';
	}

	/**
	 * @inheritDoc
	 */
	public function get_label() {
		return esc_html__( 'User Security Profiles', 'it-l10n-ithemes-security-pro' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_size() {
		return array(
			'minW'     => 3,
			'minH'     => 2,
			'maxW'     => 4,
			'maxH'     => 4,
			'defaultW' => 3,
			'defaultH' => 2,
		);
	}

	public function get_query_args() {
		$args = parent::get_query_args();

		$args['search'] = [
			'type' => 'string',
		];

		if ( ! is_multisite() ) {
			$args['roles'] = [
				'type'        => 'array',
				'items'       => [
					'type' => 'string',
				],
				'uniqueItems' => true,
				'default'     => array_values( array_filter( array_keys( wp_roles()->roles ), static function ( $role ) {
					return 'administrator' === $role || 'administrator' === ITSEC_Lib_Canonical_Roles::get_canonical_role_from_role( $role );
				} ) ),
			];
		}

		return $args;
	}

	/**
	 * @inheritDoc
	 */
	public function query_for_data( array $query_args, array $settings ) {

		$users = array();

		$user_query_args = array(
			'blog_id'  => is_multisite() ? null : get_current_blog_id(),
			'number'   => 250,
			'role__in' => $query_args['roles'] ?? [],
			'search'   => $query_args['search'] ?? '',
		);

		$user_query = new WP_User_Query( $user_query_args );

		foreach ( $user_query->get_results() as $user ) {
			$users[ $user->ID ] = $this->build_user_data( $user );
		}

		return array(
			'users' => array_values( $users ),
		);
	}

	public function get_links() {
		$links = parent::get_links();

		$links[] = [
			'rel'      => ITSEC_Lib_REST::LINK_REL . 'force-password-change',
			'route'    => 'force-password-change',
			'title'    => __( 'Force Password Change for All Users', 'it-l10n-ithemes-security-pro' ),
			'methods'  => WP_REST_Server::CREATABLE,
			'cap'      => ITSEC_Core::get_required_cap(),
			'callback' => function () {
				ITSEC_Modules::set_setting( 'password-expiration', 'expire_force', true );

				return Result::success()->add_success_message( __( 'Passwords will be reset on next login.', 'it-l10n-ithemes-security-pro' ) );
			},
		];

		if ( ITSEC_Modules::get_setting( 'password-expiration', 'expire_force' ) ) {
			$links[] = [
				'rel'      => ITSEC_Lib_REST::LINK_REL . 'clear-password-change',
				'route'    => 'clear-password-change',
				'title'    => __( 'Clear Pending Password Change', 'it-l10n-ithemes-security-pro' ),
				'methods'  => WP_REST_Server::CREATABLE,
				'cap'      => ITSEC_Core::get_required_cap(),
				'callback' => function () {
					ITSEC_Modules::set_setting( 'password-expiration', 'expire_force', false );

					return Result::success()->add_success_message( __( 'Passwords reset is no longer required.', 'it-l10n-ithemes-security-pro' ) );
				},
			];
		}

		return $links;
	}
}
