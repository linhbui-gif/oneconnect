/**
 * WordPress dependencies
 */
import { setLocaleData } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { useDispatch } from '@wordpress/data';

// Silence warnings until JS i18n is stable.
setLocaleData( { '': {} }, 'ithemes-security-pro' );

/**
 * Internal dependencies
 */
import { useSingletonEffect } from '@ithemes/security-hocs';
import { slug as listSlug, settings as listSettings } from './dashboard/list';
import {
	slug as pinnedSlug,
	settings as pinnedSettings,
} from './dashboard/pinned';
import './dashboard/style.scss';

registerPlugin( 'itsec-user-security-check-dashboard', {
	render() {
		return <App />;
	},
} );

function App() {
	const { registerCard } = useDispatch( 'ithemes-security/dashboard' );
	useSingletonEffect( App, () => {
		registerCard( listSlug, listSettings );
		registerCard( pinnedSlug, pinnedSettings );
	} );

	return null;
}
