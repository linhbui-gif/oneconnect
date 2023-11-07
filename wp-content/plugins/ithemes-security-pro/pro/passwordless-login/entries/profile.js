/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { setLocaleData } from '@wordpress/i18n';

// Silence warnings until JS i18n is stable.
setLocaleData( { '': {} }, 'ithemes-security-pro' );

/**
 * Internal dependencies
 */
import App from './profile/app.js';

export { PasswordlessLoginProfileFill } from './profile/app';
export { App };

domReady( () => {
	const el = document.getElementById( 'itsec-passwordless-login-profile-root' );

	if ( el ) {
		render( <App userId={ Number.parseInt( el.dataset.user, 10 ) } />, el );
	}
} );
