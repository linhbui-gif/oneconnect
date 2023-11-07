<?php

final class ITSEC_Version_Management {
	private static $instance;

	private $settings;

	private function __construct() {
		global $pagenow;

		$this->settings = ITSEC_Modules::get_settings( 'version-management' );

		if ( $this->settings['update_if_vulnerable'] ) {
			add_action( 'itsec_software_vulnerabilities_changed', array( $this, 'schedule_immediate_automatic_update' ) );
		}

		add_filter( 'auto_update_plugin', array( $this, 'auto_update_plugin' ), 20, 2 );
		add_filter( 'auto_update_theme', array( $this, 'auto_update_theme' ), 20, 2 );

		if ( 'none' !== $this->settings['plugin_automatic_updates'] ) {
			add_filter( 'plugins_auto_update_enabled', '__return_false' );
		}

		if ( 'none' !== $this->settings['theme_automatic_updates'] ) {
			add_filter( 'themes_auto_update_enabled', '__return_false' );
		}

		add_action( 'set_site_transient_update_plugins', array( $this, 'watch_plugin_updates' ), 100 );
		add_action( 'set_site_transient_update_themes', array( $this, 'watch_theme_updates' ), 100 );

		if ( $this->settings['scan_for_old_wordpress_sites'] ) {
			add_action( 'itsec_scheduled_old-site-scan', array( $this, 'scan_for_old_sites' ) );
		}

		add_action( 'itsec_scheduled_outdated-software', array( $this, 'check_for_outdated_software' ) );
		add_action( 'upgrader_process_complete', array( $this, 'check_for_outdated_software' ), 100 );

		add_action( 'upgrader_process_complete', array( $this, 'log_updates' ), - 100, 2 );
		add_action( '_core_updated_successfully', array( $this, 'log_core_update' ) );
		add_action( 'automatic_updates_complete', array( $this, 'log_auto_update' ) );

		add_filter( 'auto_plugin_update_send_email', array( $this, 'maybe_enable_automatic_updates_debug_email' ), 100 );
		add_filter( 'auto_theme_update_send_email', array( $this, 'maybe_enable_automatic_updates_debug_email' ), 100 );
		add_filter( 'auto_plugin_theme_update_email', array( $this, 'filter_automatic_updates_debug_email' ), 100 );

		add_action( 'itsec_scheduler_register_events', array( __CLASS__, 'register_events' ) );

		add_filter( 'itsec_notifications', array( $this, 'register_notifications' ) );
		add_filter( 'itsec_old-site-scan_notification_strings', array( $this, 'old_site_scan_strings' ) );
		add_filter( 'itsec_automatic-updates-debug_notification_strings', array( $this, 'automatic_updates_strings' ) );

		if ( 'plugins.php' === $pagenow ) {
			$packages = ITSEC_Modules::get_setting( 'version-management', 'packages' );

			foreach ( $packages as $package => $config ) {
				list( $kind, $file ) = explode( ':', $package );

				if ( 'plugin' !== $kind || 'delay' !== $config['type'] ) {
					continue;
				}

				add_action( "in_plugin_update_message-{$file}", array( $this, 'plugin_update_row' ), 10, 2 );
			}
		}
	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Schedules an "immediate" event to run the Automatic Updater.
	 *
	 * This occurs whenever the Site Scanner discovers new vulnerabilities.
	 *
	 * @param array $vulnerabilities The new vulnerabilities.
	 */
	public function schedule_immediate_automatic_update( $vulnerabilities ) {
		// If we have no vulnerabilities, don't schedule an update.
		if ( ! $vulnerabilities ) {
			return;
		}

		wp_schedule_single_event( time() + ( 5 * MINUTE_IN_SECONDS ), 'wp_maybe_auto_update' );
	}

	/**
	 * Register the events.
	 *
	 * @param ITSEC_Scheduler|null $scheduler
	 */
	public static function register_events( $scheduler = null ) {
		$scheduler = $scheduler ?: ITSEC_Core::get_scheduler();
		$scheduler->register_events_for_config( ITSEC_Modules::get_config( 'version-management' ) );
	}

	/**
	 * Whenever the update plugins transient changes, store the request time for the newly available versions if we haven't
	 * seen then before.
	 *
	 * @param object $transient
	 */
	public function watch_plugin_updates( $transient ) {

		if ( ! is_object( $transient ) || ! isset( $transient->response ) || ! is_array( $transient->response ) || empty( $transient->response ) ) {
			return;
		}

		$first_seen = ITSEC_Modules::get_setting( 'version-management', 'first_seen' );

		foreach ( $transient->response as $file => $package ) {
			if ( 0 !== validate_file( $file ) ) {
				continue;
			}

			if ( ! ITSEC_Lib::str_ends_with( $file, '.php' ) ) {
				continue;
			}

			if ( ! isset( $first_seen['plugin'][ $file ][ $package->new_version ] ) ) {
				$first_seen['plugin'][ $file ] = array( $package->new_version => $transient->last_checked );
			}
		}

		ITSEC_Modules::set_setting( 'version-management', 'first_seen', $first_seen );
	}

	/**
	 * Whenever the update themes transient changes, store the request time for the newly available versions if we haven't
	 * seen then before.
	 *
	 * @param object $transient
	 */
	public function watch_theme_updates( $transient ) {
		if ( ! is_object( $transient ) || ! isset( $transient->response ) || ! is_array( $transient->response ) || empty( $transient->response ) ) {
			return;
		}

		$first_seen = ITSEC_Modules::get_setting( 'version-management', 'first_seen' );

		foreach ( $transient->response as $file => $package ) {
			if ( ! isset( $first_seen['theme'][ $file ][ $package['new_version'] ] ) ) {
				$first_seen['theme'][ $file ] = array( $package['new_version'] => $transient->last_checked );
			}
		}

		ITSEC_Modules::set_setting( 'version-management', 'first_seen', $first_seen );
	}

	/**
	 * Check if we should auto-update this plugin.
	 *
	 * @param bool   $update
	 * @param object $item
	 *
	 * @return bool
	 */
	public function auto_update_plugin( $update, $item ) {
		if ( $update ) {
			return true;
		}

		if ( empty( $item->plugin ) ) {
			return $update;
		}

		require_once( dirname( __FILE__ ) . '/utility.php' );

		$should = ITSEC_VM_Utility::should_auto_update_plugin( $item->plugin, isset( $item->new_version ) ? $item->new_version : '' );

		if ( null === $should ) {
			return $update;
		}

		return $should;
	}

	/**
	 * Check if we should auto-update this theme.
	 *
	 * @param bool   $update
	 * @param object $item
	 *
	 * @return bool
	 */
	public function auto_update_theme( $update, $item ) {
		if ( $update ) {
			return true;
		}

		if ( empty( $item->theme ) ) {
			return $update;
		}

		require_once( dirname( __FILE__ ) . '/utility.php' );

		$should = ITSEC_VM_Utility::should_auto_update_theme( $item->theme, isset( $item->new_version ) ? $item->new_version : '' );

		if ( null === $should ) {
			return $update;
		}

		return $should;
	}

	/**
	 * Display a notice when the plugin will be auto-updated by ITSEC on the Admin Plugins screen.
	 *
	 * @param array  $plugin_data
	 * @param object $response
	 */
	public function plugin_update_row( $plugin_data, $response ) {
		if ( ! isset( $response->plugin ) ) {
			return;
		}

		if ( 'custom' !== ITSEC_Modules::get_setting( 'version-management', 'plugin_automatic_updates' ) ) {
			return;
		}

		$first_seen = ITSEC_Modules::get_setting( 'version-management', 'first_seen' );
		$packages   = ITSEC_Modules::get_setting( 'version-management', 'packages' );

		if ( isset( $first_seen['plugin'][ $response->plugin ][ $response->new_version ] ) ) {
			$time = $first_seen['plugin'][ $response->plugin ][ $response->new_version ];
			$days = $packages["plugin:{$response->plugin}"]['delay'];

			$url = esc_url( network_admin_url( 'admin.php?page=itsec&module=version-management' ) );

			if ( $time + $days * DAY_IN_SECONDS < ITSEC_Core::get_current_time_gmt() ) {
				printf( ' ' . esc_html__( 'This plugin will automatically update %1$sshortly%2$s.', 'it-l10n-ithemes-security-pro' ), "<a href=\"{$url}\">", '</a>' );
			} else {
				$diff = human_time_diff( $time + $days * DAY_IN_SECONDS );
				$link = "<a href=\"{$url}\">{$diff}</a>";

				printf( ' ' . esc_html__( 'This plugin will automatically update in %s.', 'it-l10n-ithemes-security-pro' ), $link );
			}
		}
	}

	/**
	 * Run the scanner to detect if outdated software is running.
	 *
	 * The scanner will not be run if the software is already marked as outdated.
	 */
	public function check_for_outdated_software() {
		require_once( dirname( __FILE__ ) . '/outdated-software-scanner.php' );

		ITSEC_VM_Outdated_Software_Scanner::run_scan();

		$this->update_outdated_software_flag();
	}

	/**
	 * Mark the site as running outdated software in this module's settings.
	 */
	public function update_outdated_software_flag() {
		require_once( dirname( __FILE__ ) . '/strengthen-site.php' );

		$is_software_outdated = ITSEC_Version_Management_Strengthen_Site::is_software_outdated();

		if ( $is_software_outdated !== $this->settings['is_software_outdated'] ) {
			$this->settings['is_software_outdated'] = $is_software_outdated;
			ITSEC_Modules::set_setting( 'version-management', 'is_software_outdated', $is_software_outdated );
		}
	}

	/**
	 * Scan for outdated sites in the same web root.
	 *
	 * This will not be run if old WordPress sites have already been detected.
	 */
	public function scan_for_old_sites() {
		require_once( dirname( __FILE__ ) . '/old-site-scanner.php' );

		ITSEC_VM_Old_Site_Scanner::run_scan();
	}

	/**
	 * Log updates.
	 *
	 * @param WP_Upgrader|Plugin_Upgrader|Theme_Upgrader $upgrader
	 * @param array                                      $context
	 */
	public function log_updates( $upgrader, $context ) {

		if ( empty( $context['type'] ) ) {
			return;
		}

		$auto = doing_action( 'wp_maybe_auto_update' ) ? 'auto' : 'manual';

		switch ( $context['type'] ) {
			case 'plugin':
				$update = get_site_transient( 'update_plugins' );

				if ( ! empty( $context['bulk'] ) ) {
					$files   = $context['plugins'];
					$install = false;
				} elseif ( 'install' === $context['action'] ) {
					$plugin  = $upgrader->plugin_info();
					$files   = $plugin ? array( $plugin ) : array();
					$install = true;
				} else {
					$files   = array( $context['plugin'] );
					$install = false;
				}

				foreach ( $files as $file ) {
					if ( isset( $update->response[ $file ]->new_version ) ) {
						$version = $update->response[ $file ]->new_version;
					} else {
						if ( ! function_exists( 'get_plugin_data' ) ) {
							require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						}

						if ( function_exists( 'get_plugin_data' ) && file_exists( WP_PLUGIN_DIR . '/' . $file ) ) {
							$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $file );
							$version     = $plugin_data['Version'];
						} else {
							$version = '-';
						}
					}

					$code = $install ? 'install' : 'update';
					ITSEC_Log::add_notice( 'version_management', "{$code}::plugin,{$file},{$version},{$auto}", compact( 'context' ) );

					if ( ! $install ) {
						ITSEC_Dashboard_Util::record_event( 'vm-update-plugin' );
					}
				}
				break;
			case 'theme':
				$update = get_site_transient( 'update_themes' );

				if ( ! empty( $context['bulk'] ) ) {
					$files   = $context['themes'];
					$install = false;
				} elseif ( 'install' === $context['action'] ) {
					$theme   = $upgrader->theme_info();
					$files   = $theme ? array( $theme->get_stylesheet() ) : array();
					$install = true;
				} else {
					$files   = array( $context['theme'] );
					$install = false;
				}

				foreach ( $files as $file ) {
					if ( isset( $update->response[ $file ]['new_version'] ) ) {
						$version = $update->response[ $file ]['new_version'];
					} elseif ( ( $theme = wp_get_theme( $file ) ) && $theme->exists() ) {
						$theme->cache_delete();
						$version = $theme->get( 'Version' );
					} else {
						$version = '-';
					}

					$code = $install ? 'install' : 'update';
					ITSEC_Log::add_notice( 'version_management', "{$code}::theme,{$file},{$version},{$auto}", compact( 'context' ) );

					if ( ! $install ) {
						ITSEC_Dashboard_Util::record_event( 'vm-update-theme' );
					}
				}
				break;
		}
	}

	/**
	 * Log WordPress core updating.
	 *
	 * @param string $new_version
	 */
	public function log_core_update( $new_version ) {
		$auto        = doing_action( 'wp_maybe_auto_update' ) ? 'auto' : 'manual';
		$old_version = $GLOBALS['wp_version'];

		ITSEC_Log::add_notice( 'version_management', "update-core::{$new_version},{$old_version},{$auto}" );
		ITSEC_Dashboard_Util::record_event( 'vm-update-core' );
	}

	/**
	 * Log any errors encountered when auto-updating.
	 *
	 * @param array $results
	 */
	public function log_auto_update( $results ) {

		$has_core = $has_error = false;

		foreach ( array( 'core', 'plugin', 'theme', 'translation' ) as $type ) {
			if ( empty( $results[ $type ] ) ) {
				continue;
			}

			foreach ( $results[ $type ] as $update ) {
				if ( ! $update->result || is_wp_error( $update->result ) ) {
					$has_error = true;
					$has_core  = $has_core || 'core' === $type;
				}
			}
		}

		if ( $has_core ) {
			$method = 'add_error';
		} elseif ( $has_error ) {
			$method = 'add_warning';
		} else {
			$method = 'add_debug';
		}

		ITSEC_Log::$method( 'version_management', 'auto-update', $results );
	}

	/**
	 * Enable the automatic update email if it is enabled in the Notification Center.
	 *
	 * @param bool $enabled
	 *
	 * @return bool
	 */
	public function maybe_enable_automatic_updates_debug_email( $enabled ) {
		// The debug email is turned off by default. We don't want to disable it if it has been enabled by another system.
		if ( $enabled && 'automatic_updates_send_debug_email' === current_filter() ) {
			return $enabled;
		}

		return ITSEC_Core::get_notification_center()->is_notification_enabled( 'automatic-updates-debug' );
	}

	/**
	 * Set automatic update email addresses.
	 *
	 * @param array $email
	 *
	 * @return array
	 */
	public function filter_automatic_updates_debug_email( $email ) {

		if ( ITSEC_Core::get_notification_center()->is_notification_enabled( 'automatic-updates-debug' ) ) {
			$email['to'] = ITSEC_Core::get_notification_center()->get_recipients( 'automatic-updates-debug' );
		}

		return $email;
	}

	public function register_notifications( $notifications ) {

		// Ask for the settings again in case of saving and adding new notifications so the cache clear happens.
		$settings = ITSEC_Modules::get_settings( 'version-management' );

		if ( $settings['wordpress_automatic_updates'] || $settings['plugin_automatic_updates'] || $settings['theme_automatic_updates'] ) {
			$notifications['automatic-updates-debug'] = array(
				'recipient' => ITSEC_Notification_Center::R_USER_LIST,
				'optional'  => true,
				'module'    => 'version-management',
			);
		}

		if ( $settings['scan_for_old_wordpress_sites'] ) {
			$notifications['old-site-scan'] = array(
				'slug'             => 'old-site-scan',
				'recipient'        => ITSEC_Notification_Center::R_USER_LIST,
				'schedule'         => ITSEC_Notification_Center::S_CONFIGURABLE,
				'subject_editable' => true,
				'module'           => 'version-management',
				'template'         => array(
					array(
						'header',
						esc_html__( 'Outdated Site Scan', 'it-l10n-ithemes-security-pro' ),
						/* translators: %s is a date range ( 1/1/16 - 2/1/16 ) */
						sprintf( esc_html__( 'Outdated sites detected on %s', 'it-l10n-ithemes-security-pro' ), '<b>{{ $_period }}</b>' )
					),
					array(
						'table',
						array(
							esc_html__( 'File Path', 'it-l10n-ithemes-security-pro' ),
							esc_html__( 'WordPress Version', 'it-l10n-ithemes-security-pro' )
						),
						array(
							':data.path',
							':data.version',
						),
					),
					array(
						'footer'
					),
				),
			);
		}

		return $notifications;
	}

	public function automatic_updates_strings() {
		return array(
			'label'       => __( 'Automatic Updates Info', 'it-l10n-ithemes-security-pro' ),
			'description' => sprintf(
				__( 'The %sVersion Management%s module will send an email with details about any automatic updates that have been performed.', 'it-l10n-ithemes-security-pro' ),
				ITSEC_Core::get_link_for_settings_route( ITSEC_Core::get_settings_module_route( 'version-management' ) ),
				'</a>'
			)
		);
	}

	public function old_site_scan_strings() {
		return array(
			'label'       => __( 'Old Site Scan', 'it-l10n-ithemes-security-pro' ),
			'description' => sprintf(
				__( 'The %1$sVersion Management%2$s module will send an email if it detects outdated WordPress sites on your hosting account. A single outdated WordPress site with a vulnerability could allow attackers to compromise all the other sites on the same hosting account.', 'it-l10n-ithemes-security-pro' ),
				ITSEC_Core::get_link_for_settings_route( ITSEC_Core::get_settings_module_route( 'version-management' ) ),
				'</a>'
			),
			'subject'     => __( 'Old sites found on hosting account', 'it-l10n-ithemes-security-pro' )
		);
	}
}

ITSEC_Version_Management::get_instance();
