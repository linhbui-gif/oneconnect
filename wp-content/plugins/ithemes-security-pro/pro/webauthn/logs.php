<?php

namespace iThemesSecurity\WebAuthn;

use ITSEC_Modules;

final class Logs {

	public function __construct() {
		add_filter( 'itsec_logs_prepare_webauthn_entry_for_list_display', array( $this, 'filter_entry_for_list_display' ), 10, 3 );
	}

	public function filter_entry_for_list_display( $entry, $code, $data ) {
		$entry['module_display'] = esc_html( ITSEC_Modules::get_labels( 'webauthn' )['title'] );

		// make sure we get a description before assigning entry description
		$entry_description = self::transform_error_code_to_readable_string( $code, $data );
		if ( $entry_description ) {
			$entry['description'] = $entry_description;
		}

		return $entry;
	}

	/**
	 * Convert Error Codes to Human Readable Format
	 *
	 * This function takes in a string from the components like user-entity and
	 * uses that string to figure out which message should be sent out to end users.
	 * The error_data array is also used to provide more specific messages.
	 *
	 * @param string $component_code The code derived from code::data1,data2,data3 where code is $component code and datas are $error_code_arr.
	 * @param array  $error_data     Array of errors containing string representing origin of error.
	 *
	 * @return string
	 */
	public static function transform_error_code_to_readable_string( string $component_code, array $error_data ): string {

		switch ( $component_code ) {
			case 'user-entity':
				switch ( $error_data[0] ) {
					case 'no-random-bytes':
						return __( 'Could not generate passkey user id.', 'it-l10n-ithemes-security-pro' );
					case 'db-error':
					case 'write-db-error':
						return __( 'Could not save passkey user id.', 'it-l10n-ithemes-security-pro' );
					case 'read-db-error':
						return __( 'Could lookup passkey user id.', 'it-l10n-ithemes-security-pro' );
				}
				break;
			case 'authentication-ceremony-failed':
				return __( 'Could not authenticate user.', 'it-l10n-ithemes-security-pro' );
			case 'hash-conflict':
				return __( 'Hash Conflict Occurred.', 'it-l10n-ithemes-security-pro' );
			case 'credential-repository':
				switch ( $error_data[0] ) {
					case 'id-available-failed':
						return __( 'Could not check if a passkey id is available.', 'it-l10n-ithemes-security-pro' );
					case 'id-find-failed':
						return __( 'Could not lookup passkey by its id', 'it-l10n-ithemes-security-pro' );
					case 'user-has-credentials-failed':
					case 'get-user-credentials-failed':
						return __( 'Could not lookup passkeys for the requested user.', 'it-l10n-ithemes-security-pro' );
					case 'delete-trashed-failed':
						return __( 'Could not delete trashed passkeys.', 'it-l10n-ithemes-security-pro' );
				}
				break;
			case 'registration-ceremony-failed':
				return __( 'Could not register a new passkey.', 'it-l10n-ithemes-security-pro' );
		}

		return '';
	}
}

new Logs();
