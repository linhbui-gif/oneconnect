<?php

namespace iThemesSecurity\Pro_Dashboard;

use iThemesSecurity\Contracts\Runnable;
use ITSEC_Dashboard;
use ITSEC_Lib;
use ITSEC_Lib_REST;

class REST implements Runnable {
	public function run() {
		register_rest_field( 'ithemes-security-dashboard', 'sharing', [
			'get_callback'    => function ( $data ) {
				$sharing = [];

				if ( $user_ids = get_post_meta( $data['id'], ITSEC_Dashboard::META_SHARE_USER ) ) {
					$sharing[] = [
						'type'  => 'user',
						'users' => array_map( 'intval', $user_ids ),
					];
				}

				if ( $roles = get_post_meta( $data['id'], ITSEC_Dashboard::META_SHARE_ROLE ) ) {
					$sharing[] = [
						'type'  => 'role',
						'roles' => $roles,
					];
				}

				return $sharing;
			},
			'update_callback' => function ( $sharing, $post ) {
				$seen = $existing = [];

				foreach ( $sharing as $share ) {
					switch ( $share['type'] ) {
						case 'user':
							$key = ITSEC_Dashboard::META_SHARE_USER;
							$new = $share['users'];
							break;
						case 'role':
							$key = ITSEC_Dashboard::META_SHARE_ROLE;
							$new = $share['roles'];
							break;
						default:
							break 2;
					}

					if ( ! isset( $existing[ $key ] ) ) {
						$existing[ $key ] = get_post_meta( $post->ID, $key );
					}

					foreach ( $new as $val ) {
						if ( in_array( $val, $existing[ $key ], false ) ) {
							$seen[ $key ][] = $val;
						} else {
							add_post_meta( $post->ID, $key, ITSEC_Lib::slash( $val ) );
						}
					}
				}

				foreach ( $existing as $key => $values ) {
					foreach ( $values as $val ) {
						if ( empty( $seen[ $key ] ) || ! in_array( $val, $seen[ $key ], false ) ) {
							delete_post_meta( $post->ID, $key, $val );
						}
					}
				}
			},
			'schema'          => [
				'context' => [ 'edit' ],
				'type'    => 'array',
				'items'   => [
					'oneOf' => [
						[
							'type'                 => 'object',
							'additionalProperties' => false,
							'properties'           => [
								'type'  => [
									'type' => 'string',
									'enum' => [ 'role' ],
								],
								'roles' => [
									'type'  => 'array',
									'items' => [
										'type' => 'string',
										'enum' => array_keys( wp_roles()->roles )
									],
								],
							],
						],
						[
							'type'                 => 'object',
							'additionalProperties' => false,
							'properties'           => [
								'type'  => [
									'type' => 'string',
									'enum' => [ 'user' ],
								],
								'users' => [
									'type'  => 'array',
									'items' => [
										'type' => 'integer',
									],
								],
							],
						],
					],
				],
			]
		] );

		add_action( 'itsec_prepare_dashboard_response', function ( $response, $post, $request ) {
			if ( 'edit' !== $request['context'] ) {
				return;
			}

			foreach ( get_post_meta( $post->ID, ITSEC_Dashboard::META_SHARE_USER ) as $user_id ) {
				$response->add_link(
					ITSEC_Lib_REST::LINK_REL . 'shared-with',
					rest_url( "wp/v2/users/{$user_id}" ),
					[ 'embeddable' => true ]
				);
			}
		}, 10, 3 );
	}
}
