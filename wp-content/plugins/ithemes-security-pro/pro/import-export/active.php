<?php

function itsec_import_export_register_sync_verbs( $api ) {
	$api->register( 'itsec-get-settings-export', 'Ithemes_Sync_Verb_ITSEC_Get_Settings_Export', dirname( __FILE__ ) . '/sync-verbs/itsec-get-settings-export.php' );
	$api->register( 'itsec-import-settings', 'Ithemes_Sync_Verb_ITSEC_Import_Settings', dirname( __FILE__ ) . '/sync-verbs/itsec-import-settings.php' );
}

add_action( 'ithemes_sync_register_verbs', 'itsec_import_export_register_sync_verbs' );

/**
 * Register the Export notification.
 *
 * @param array $notifications
 *
 * @return array
 */
function itsec_import_export_register_notification( $notifications ) {

	$notifications['import-export'] = array(
		'subject_editable' => true,
		'message_editable' => true,
		'recipient'        => ITSEC_Notification_Center::R_PER_USE,
		'schedule'         => ITSEC_Notification_Center::S_NONE,
		'tags'             => array( 'date', 'time', 'site_title', 'site_url' ),
		'module'           => 'import-export',
	);

	return $notifications;
}

add_filter( 'itsec_notifications', 'itsec_import_export_register_notification' );

/**
 * Register the Export notification strings.
 *
 * @return array
 */
function itsec_import_export_notification_strings() {
	return array(
		'label'       => __( 'Settings Export', 'it-l10n-ithemes-security-pro' ),
		'description' => __( 'The Import Export feature sends an email with the settings export file attached.', 'it-l10n-ithemes-security-pro' ),
		'subject'     => __( 'Security Settings Export', 'it-l10n-ithemes-security-pro' ),
		'message'     => __( 'Attached is the settings file for {{ $site_url }} created on {{ $date }} at {{ $time }}.', 'it-l10n-ithemes-security-pro' ),
		'tags'        => array(
			'date'       => __( 'The date the settings export was generated.', 'it-l10n-ithemes-security-pro' ),
			'time'       => __( 'The time the settings export was generated.', 'it-l10n-ithemes-security-pro' ),
			'site_url'   => __( 'The URL to your website.', 'it-l10n-ithemes-security-pro' ),
			'site_title' => __( 'The WordPress Site Title. Can be changed under Settings → General → Site Title', 'it-l10n-ithemes-security-pro' ),
		),
	);
}

add_filter( 'itsec_import-export_notification_strings', 'itsec_import_export_notification_strings' );
