/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

/**
 * Internal dependencies
 */
import { App } from '@ithemes/security.passwordless-login.profile';

domReady( () => {
	const el = document.getElementById( 'itsec-passwordless-login-frontend-root' );

	if ( el ) {
		const styleSheet = document.getElementById( 'wp-components-css' );

		if ( styleSheet.parentElement.tagName !== 'HEAD' ) {
			// Move @wordpress/components CSS to the head, so it doesn't
			// have a greater specificity than emotion styles.
			document.head.appendChild( styleSheet );
		}

		const userId = Number.parseInt( el.dataset.user, 10 );
		render( <App userId={ userId } useShadow />, el );
	}
} );
