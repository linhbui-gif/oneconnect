/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { setLocaleData } from '@wordpress/i18n';
import { dispatch, resolveSelect } from '@wordpress/data';

// Silence warnings until JS i18n is stable.
setLocaleData( { '': {} }, 'ithemes-security-pro' );

/**
 * Internal dependencies
 */
import { isAvailable } from '@ithemes/security.webauthn.utils';
import { App, store } from '@ithemes/security.webauthn.manage';
import './login/style.scss';

domReady( async () => {
	const el = document.getElementById( 'itsec-webauthn-login' );

	if ( ! el || ! isAvailable() ) {
		window.itsecLoginInterstitial.submitToProceed();

		return;
	}

	const isRequested = el.dataset.isRequested === '1';

	if ( ! isRequested && ! ( await resolveSelect( store ).canRegisterPlatformAuthenticator() ) ) {
		window.itsecLoginInterstitial.submitToProceed();

		return;
	}

	dispatch( store ).navigateTo( isRequested ? 'manage-credentials' : 'add-credential' );

	const onExit = () => window.itsecLoginInterstitial.submitToProceed( { itsec_skip: 1 } );
	const onComplete = () => window.itsecLoginInterstitial.submitToProceed();

	render( <App onExit={ onExit } onComplete={ onComplete } isRequested={ isRequested } />, el );
} );
