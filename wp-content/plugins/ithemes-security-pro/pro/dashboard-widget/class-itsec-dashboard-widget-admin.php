<?php

class ITSEC_Dashboard_Widget_Admin {

	public function run() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Execute all hooks on admin init
	 *
	 * All hooks on admin init to make certain user has the correct permissions
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function admin_init() {
		if ( ! ITSEC_Core::current_user_can_manage() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'register_widgets' ) );
		add_action( 'wp_network_dashboard_setup', array( $this, 'register_widgets' ) );
	}

	/**
	 * Create the dashboard widget.
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function register_widgets() {
		if ( ! ITSEC_Modules::get_setting( 'global', 'onboard_complete' ) ) {
			return;
		}

		ITSEC_Modules::load_module_file( 'class-itsec-dashboard-util.php', 'dashboard' );

		if ( ! ITSEC_Dashboard_Util::get_primary_dashboard_id() ) {
			return;
		}

		$name = ITSEC_Core::get_plugin_name();
		wp_add_dashboard_widget( 'itsec-dashboard-widget', $name, array( $this, 'render_dashboard_widget' ) );
	}

	/**
	 * Add JavaScript to power the dashboard widget.
	 *
	 * @since 1.9
	 *
	 * @param string $hook
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( 'index.php' !== $hook ) {
			return;
		}

		if ( ! ITSEC_Modules::get_setting( 'global', 'onboard_complete' ) ) {
			return;
		}

		ITSEC_Modules::load_module_file( 'class-itsec-dashboard-util.php', 'dashboard' );

		if ( ! ITSEC_Dashboard_Util::get_primary_dashboard_id() ) {
			return;
		}

		wp_enqueue_style( 'itsec-dashboard-dashboard' );
		wp_enqueue_style( 'itsec-dashboard-widget-widget' );
		wp_enqueue_script( 'itsec-dashboard-widget-widget' );

		foreach ( ITSEC_Modules::get_available_modules() as $module ) {
			if ( ! ITSEC_Modules::is_active( $module ) ) {
				continue;
			}

			$handle = "itsec-{$module}-dashboard";

			if ( wp_script_is( $handle, 'registered' ) ) {
				wp_enqueue_script( $handle );
			}

			if ( wp_style_is( $handle, 'registered' ) ) {
				wp_enqueue_style( $handle );
			}
		}
	}

	/**
	 * Render the dashboard widget root.
	 */
	public function render_dashboard_widget() {
		echo '<div id="itsec-widget-root"></div>';
	}
}
