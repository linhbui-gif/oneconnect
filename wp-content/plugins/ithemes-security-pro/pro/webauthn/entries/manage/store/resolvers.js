/**
 * Internal dependencies
 */
import { apiFetch, awaitPromise } from '@ithemes/security.packages.data';
import { isPlatformAuthenticatorAvailable } from '@ithemes/security.webauthn.utils';
import { receiveCredentials } from './actions';

export function *getCredentials() {
	try {
		const response = yield apiFetch( {
			path: '/ithemes-security/v1/webauthn/credentials',
		} );
		yield receiveCredentials( response );
	} catch ( error ) {
		yield {
			type: 'FAILED_FETCH_CREDENTIALS',
			error,
		};
	}
}

export function *canRegisterPlatformAuthenticator() {
	const isAvailable = yield awaitPromise( isPlatformAuthenticatorAvailable() );

	yield { type: 'RECEIVE_CAN_REGISTER_PLATFORM_AUTHENTICATOR', isAvailable };
}
