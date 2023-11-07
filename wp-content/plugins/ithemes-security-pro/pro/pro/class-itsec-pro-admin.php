<?php

class ITSEC_Pro_Admin {

	function run() {
		add_filter( 'itsec_meta_links', array( $this, 'add_plugin_meta_links' ) );
		add_filter( 'itsec_support_url', array( $this, 'itsec_support_url' ) );
	}

	/**
	 * Adds links to the plugin row meta
	 *
	 * @since 4.0
	 *
	 * @param array $meta Existing meta
	 *
	 * @return array
	 */
	public function add_plugin_meta_links( $meta ) {

		$meta[] = '<a href="http://ithemes.com/member/support.php" target="_blank" rel="noopener noreferrer">' . __( 'Get Support', 'it-l10n-ithemes-security-pro' ) . '</a>';

		return $meta;
	}

	public function itsec_support_url( $support_url ) {
		return 'http://ithemes.com/member/support.php';
	}
}
