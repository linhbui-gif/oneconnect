/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';

export default function CloseButton( { close } ) {
	return (
		<Button
			className="itsec-close-button"
			icon="no-alt"
			onClick={ ( e ) => {
				e.preventDefault();
				close();
			} }
			tooltip={ false }
			label={ __( 'Close', 'it-l10n-ithemes-security-pro' ) }
		/>
	);
}
