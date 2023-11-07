/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

function arrayToBase64UrlString( a ) {
	return window
		.btoa( String.fromCharCode( ...a ) )
		.replace( /\+/g, '-' )
		.replace( /\//g, '_' );
}

function base64UrlStringToArray( str ) {
	return Uint8Array.from(
		base64UrlDecode( str ),
		( c ) => c.charCodeAt( 0 )
	);
}

function base64UrlDecode( input ) {
	input = input
		.replace( /-/g, '+' )
		.replace( /_/g, '/' );

	const pad = input.length % 4;
	if ( pad ) {
		if ( pad === 1 ) {
			throw new Error( 'InvalidLengthError: Input base64url string is the wrong length to determine padding' );
		}
		input += new Array( 5 - pad ).join( '=' );
	}

	return window.atob( input );
}

/**
 * Prepares a Public Key Creation Options dictionary.
 *
 * @param {Object} data Data provided from the Relying Party server.
 * @return {window.PublicKeyCredentialCreationOptions} Prepared options.
 */
export function preparePublicKeyCreationOptions( data ) {
	const options = {
		challenge: base64UrlStringToArray( data.challenge ),
		rp: data.rp,
		user: {
			...data.user,
			id: base64UrlStringToArray( data.user.id ),
		},
		pubKeyCredParams: data.pubKeyCredParams,
	};

	if ( data.excludeCredentials !== undefined ) {
		options.excludeCredentials = data.excludeCredentials.map(
			( credential ) => ( {
				...credential,
				id: base64UrlStringToArray( credential.id ),
			} )
		);
	}

	if ( data.authenticatorSelection ) {
		options.authenticatorSelection = data.authenticatorSelection;
	}

	if ( data.attestation ) {
		options.attestation = data.attestation;
	}

	return options;
}

/**
 * Prepares a Public Key Request Options dictionary.
 *
 * @param {Object} data Data provided from the Relying Party server.
 * @return {window.PublicKeyCredentialRequestOptions} Prepared options.
 */
export function preparePublicKeyRequestOptions( data ) {
	const options = {
		challenge: base64UrlStringToArray( data.challenge ),
	};

	if ( data.rpId !== undefined ) {
		options.rpId = data.rpId;
	}

	if ( data.allowCredentials !== undefined ) {
		options.allowCredentials = data.allowCredentials.map(
			( credential ) => ( {
				...credential,
				id: base64UrlStringToArray( credential.id ),
			} )
		);
	}

	if ( data.userVerification ) {
		options.userVerification = data.userVerification;
	}

	return options;
}

export function preparePublicKeyCredentials( data ) {
	const publicKeyCredential = {
		id: data.id,
		type: data.type,
		rawId: arrayToBase64UrlString( new Uint8Array( data.rawId ) ),
		response: {
			clientDataJSON: arrayToBase64UrlString(
				new Uint8Array( data.response.clientDataJSON )
			),
		},
	};

	if ( data.response.attestationObject !== undefined ) {
		publicKeyCredential.response.attestationObject = arrayToBase64UrlString(
			new Uint8Array( data.response.attestationObject )
		);
	}

	if ( data.response.authenticatorData !== undefined ) {
		publicKeyCredential.response.authenticatorData = arrayToBase64UrlString(
			new Uint8Array( data.response.authenticatorData )
		);
	}

	if ( data.response.signature !== undefined ) {
		publicKeyCredential.response.signature = arrayToBase64UrlString(
			new Uint8Array( data.response.signature )
		);
	}

	if ( data.response.userHandle !== undefined ) {
		publicKeyCredential.response.userHandle = arrayToBase64UrlString(
			new Uint8Array( data.response.userHandle )
		);
	}

	if ( data.response.getTransports ) {
		publicKeyCredential.response.transports = data.response.getTransports();
	}

	return publicKeyCredential;
}

export function isAvailable() {
	return window.navigator.credentials && window.PublicKeyCredential;
}

/**
 * Checks if a Platform Authenticator is available.
 *
 * @return {Promise<boolean>} Promise resolving to True if available.
 */
export function isPlatformAuthenticatorAvailable() {
	if ( ! isAvailable() ) {
		return Promise.resolve( false );
	}

	return window.PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
}

export async function registerCredential( { label, authenticatorSelection } ) {
	const registerResponse = await apiFetch( {
		path: '/ithemes-security/rpc/webauthn/register-credential',
		method: 'POST',
		data: {
			authenticatorSelection,
		},
	} );

	const options = preparePublicKeyCreationOptions( registerResponse );

	const credential = await window.navigator.credentials.create( { publicKey: options } );

	if ( ! ( credential.response instanceof window.AuthenticatorAttestationResponse ) ) {
		throw new Error( 'Invalid response format.' );
	}

	const url = registerResponse._links?.[ 'ithemes-security:webauthn-create-credential' ]?.[ 0 ]?.href;
	const prepared = preparePublicKeyCredentials( credential );

	return await apiFetch( {
		url,
		method: 'POST',
		data: {
			credential: prepared,
			label,
		},
	} );
}

export async function verifyCredential( user ) {
	const verifyResponse = await apiFetch( {
		path: '/ithemes-security/rpc/webauthn/verify-credential',
		method: 'POST',
		data: {
			user,
		},
		credentials: 'omit',
	} );

	const options = preparePublicKeyRequestOptions( verifyResponse );

	const credential = await window.navigator.credentials.get( { publicKey: options } );

	if ( ! ( credential.response instanceof window.AuthenticatorAssertionResponse ) ) {
		throw new Error( 'Invalid response format.' );
	}

	const url = verifyResponse._links?.[ 'ithemes-security:webauthn-verify-credential' ]?.[ 0 ]?.href;
	const prepared = preparePublicKeyCredentials( credential );

	return await apiFetch( {
		url,
		method: 'POST',
		data: {
			credential: prepared,
			user,
		},
		credentials: 'omit',
	} );
}
