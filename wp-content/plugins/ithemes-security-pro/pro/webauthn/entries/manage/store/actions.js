/**
 * WordPress dependencies
 */
import { controls } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { awaitPromise, apiFetch } from '@ithemes/security.packages.data';
import { STORE_NAME } from './';

export function navigateTo( screen ) {
	return {
		type: 'NAVIGATE_TO',
		screen,
	};
}

export function receiveCredentials( items ) {
	return {
		type: 'RECEIVE_CREDENTIALS',
		items,
	};
}

/**
 * Begins the flow to register a new passkey.
 *
 * @param {string}  key
 * @param {Promise} registerPromise Promise returned from the WebAuthn API.
 */
export function *registerCredential( key, registerPromise ) {
	yield { type: 'START_REGISTER_CREDENTIAL', key };
	try {
		const credential = yield awaitPromise( registerPromise );
		yield { type: 'FINISH_REGISTER_CREDENTIAL', key, item: credential };

		return credential;
	} catch ( error ) {
		yield { type: 'FAILED_REGISTER_CREDENTIAL', key, error };

		throw error;
	}
}

export function *persistCredential( id, credential ) {
	yield { type: 'START_PERSIST_CREDENTIAL', id };

	try {
		const response = yield apiFetch( {
			method: 'PUT',
			path: `/ithemes-security/v1/webauthn/credentials/${ id }`,
			data: credential,
		} );
		yield { type: 'FINISH_PERSIST_CREDENTIAL', id, item: response };

		return response;
	} catch ( error ) {
		yield { type: 'FAILED_PERSIST_CREDENTIAL', id, error };

		throw error;
	}
}

export function *deleteCredential( id, force = false ) {
	yield { type: 'START_DELETE_CREDENTIAL', id };
	try {
		const response = yield apiFetch( {
			method: 'DELETE',
			path: `/ithemes-security/v1/webauthn/credentials/${ id }?force=${ force }`,
		} );
		yield { type: 'FINISH_DELETE_CREDENTIAL', id, force, item: response };

		return true;
	} catch ( error ) {
		yield { type: 'FAILED_DELETE_CREDENTIAL', id, error };

		throw error;
	}
}

export function *restoreCredential( id ) {
	yield *persistCredential( id, { status: 'active' } );
}

export function *persistNewCredentialLabel( label ) {
	const credential = yield controls.select( STORE_NAME, 'getNewCredential' );

	if ( ! credential ) {
		return;
	}

	yield *persistCredential( credential.id, { label } );
}
