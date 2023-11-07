/**
 * WordPress dependencies
 */
import { registerPlugin } from '@wordpress/plugins';
import { setLocaleData } from '@wordpress/i18n';

// Silence warnings until JS i18n is stable.
setLocaleData( { '': {} }, 'it-l10n-ithemes-security-pro' );

/**
 * Internal dependencies
 */
import App from './dashboard/app';

registerPlugin( 'itsec-pro-dashboard', {
	render() {
		return <App />;
	},
} );
