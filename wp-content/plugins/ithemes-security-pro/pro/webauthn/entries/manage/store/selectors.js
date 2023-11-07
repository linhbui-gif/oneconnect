/**
 * External dependencies
 */
import createSelector from 'rememo';

export function getScreen( state ) {
	return state.screen;
}

export function isRegistering( state, key ) {
	if ( key ) {
		return state.isRegistering === key;
	}

	return state.isRegistering;
}

export function getRegisterError( state ) {
	return state.registerError;
}

export const getCredentials = createSelector(
	( state ) => state.credentials.map( ( id ) => state.byId[ id ] ),
	( state ) => [ state.credentials, state.byId ]
);

export function getFetchCredentialsError( state ) {
	return state.fetchError;
}

export function getCredentialError( state, id ) {
	return state.credentialErrors[ id ];
}

export function isPersisting( state, id ) {
	return state.persistingIds.includes( id );
}

export function isDeleting( state, id ) {
	return state.deletingIds.includes( id );
}

export function getNewCredentialId( state ) {
	return state.newCredential;
}

export function getNewCredential( state ) {
	return state.byId[ state.newCredential ];
}

export const getCredentialErrors = createSelector(
	( state ) => Object.values( state.credentialErrors ),
	( state ) => state.credentialErrors
);

export function canRegisterPlatformAuthenticator( state ) {
	return state.canRegisterPlatformAuthenticator;
}
