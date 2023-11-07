/**
 * External dependencies
 */
import { map, keyBy, without, omit } from 'lodash';

const DEFAULT_STATE = {
	screen: 'add-credential',
	isRegistering: false,
	registerError: null,
	credentials: [],
	fetchError: null,
	byId: {},
	newCredential: null,
	persistingIds: [],
	deletingIds: [],
	credentialErrors: {},
	canRegisterPlatformAuthenticator: false,
};

export default function( state = DEFAULT_STATE, action ) {
	switch ( action.type ) {
		case 'NAVIGATE_TO':
			return {
				...state,
				screen: action.screen,
			};
		case 'START_REGISTER_CREDENTIAL':
			return {
				...state,
				isRegistering: action.key,
				registerError: null,
			};
		case 'FINISH_REGISTER_CREDENTIAL':
			return {
				...state,
				credentials: [
					...state.credentials,
					action.item.id,
				],
				byId: {
					...state.byId,
					[ action.item.id ]: action.item,
				},
				newCredential: action.item.id,
				isRegistering: false,
			};
		case 'FAILED_REGISTER_CREDENTIAL':
			return {
				...state,
				isRegistering: false,
				registerError: action.error,
			};
		case 'RECEIVE_CREDENTIAL':
			return {
				...state,
				byId: {
					...state.byId,
					[ action.item.id ]: action.item,
				},
			};
		case 'RECEIVE_CREDENTIALS':
			return {
				...state,
				credentials: map( action.items, 'id' ),
				byId: keyBy( action.items, 'id' ),
			};
		case 'FAILED_FETCH_CREDENTIALS':
			return {
				...state,
				fetchError: action.error,
			};
		case 'START_PERSIST_CREDENTIAL':
			return {
				...state,
				persistingIds: [
					...state.persistingIds,
					action.id,
				],
				credentialErrors: omit( state.credentialErrors, action.id ),
			};
		case 'FINISH_PERSIST_CREDENTIAL':
			return {
				...state,
				persistingIds: without( state.persistingIds, action.id ),
				byId: {
					...state.byId,
					[ action.id ]: action.item,
				},
			};
		case 'FAILED_PERSIST_CREDENTIAL':
			return {
				...state,
				persistingIds: without( state.persistingIds, action.id ),
				credentialErrors: {
					...state.credentialErrors,
					[ action.id ]: action.error,
				},
			};
		case 'START_DELETE_CREDENTIAL':
			return {
				...state,
				deletingIds: [ ...state.deletingIds, action.id ],
				credentialErrors: omit( state.credentialErrors, action.id ),
			};
		case 'FINISH_DELETE_CREDENTIAL':
			return {
				...state,
				deletingIds: without( state.deletingIds, action.id ),
				credentials: action.force ? without( state.credentials, action.id ) : state.credentials,
				byId: action.force ? omit( state.byId, action.id ) : {
					...state.byId,
					[ action.id ]: action.item,
				},
			};
		case 'FAILED_DELETE_CREDENTIAL':
			return {
				...state,
				deletingIds: without( state.deletingIds, action.id ),
				credentialErrors: {
					...state.credentialErrors,
					[ action.id ]: action.error,
				},
			};
		case 'RECEIVE_CAN_REGISTER_PLATFORM_AUTHENTICATOR':
			return {
				...state,
				canRegisterPlatformAuthenticator: action.isAvailable,
			};
		default:
			return state;
	}
}
