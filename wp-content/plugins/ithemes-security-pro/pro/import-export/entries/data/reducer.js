/**
 * External dependencies
 */
import { has } from 'lodash';

/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';

/**
 * Internal dependencies
 */
import {
	FINISH_EXPORT,
	DELETE_EXPORT,
	RECEIVE_EXPORTS,
	RECEIVE_SOURCES,
	SET_EXPORT_ERROR,
	CREATE_EXPORT,
	VALIDATE_EXPORT_FILE,
	VALIDATE_EXPORT_DATA,
	VALIDATED_EXPORT,
	WPC_ENTER_WEBSITE,
	WPC_DISCOVER_REST_API,
	WPC_DISCOVERED_REST_API,
	WPC_APPLICATION_APPROVED,
	WPC_APPLICATION_REJECTED,
	WPC_RESET,
	WPC_CHECK_SUPPORT,
	WPC_RECEIVE_EXPORTS,
	WPC_RECEIVE_SOURCES,
	WPC_CREATE_EXPORT,
	WPC_FINISH_EXPORT,
	WPC_RECEIVE_APP_PASSWORD,
	EDIT_IMPORT_SOURCES,
	EDIT_IMPORT_USER_MAP,
	EDIT_IMPORT_ROLE_MAP,
	IMPORT_RESET,
	WPC_DELETE_CREDENTIALS,
	WPC_DELETE_CREDENTIALS_SUCCESS,
} from './actions';

const WPC_DEFAULT_STATE = {
	// enterWebsite, discovering, noAppPasswords
	// awaitingAuth, appRejected, appAuthed
	// hasSupport, noSupport
	step: 'enterWebsite',
	siteUrl: '',
	discovery: null,
	exports: null,
	sources: null,
	isCreating: false,
	isDeleting: false,
	createdExport: null,
	introspection: null,
};

function wpConnect( state = WPC_DEFAULT_STATE, action ) {
	switch ( action.type ) {
		case WPC_RESET:
			return {
				...WPC_DEFAULT_STATE,
			};
		case WPC_ENTER_WEBSITE:
			return {
				...WPC_DEFAULT_STATE,
				siteUrl: action.url,
			};
		case WPC_DISCOVER_REST_API:
			return {
				...state,
				step: 'discovering',
			};
		case WPC_DISCOVERED_REST_API:
			return {
				...state,
				discovery: action.result,
				step:
					action.result.isSuccess() &&
					has( action.result, [
						'data',
						'index',
						'authentication',
						'application-passwords',
					] )
						? 'awaitingAuth'
						: 'noAppPasswords',
			};
		case WPC_APPLICATION_REJECTED:
			return {
				...state,
				step: 'appRejected',
			};
		case WPC_APPLICATION_APPROVED:
			const isSameUrl = action.url === state.discovery.data.index.url;

			return {
				...WPC_DEFAULT_STATE,
				discovery: isSameUrl ? state.discovery : null,
				step: 'appAuthed',
				siteUrl: action.url,
			};
		case WPC_CHECK_SUPPORT:
			return {
				...state,
				discovery: action.discovery,
				step: action.hasSupport ? 'hasSupport' : 'noSupport',
			};
		case WPC_RECEIVE_EXPORTS:
			return {
				...state,
				exports: action.exports,
			};
		case WPC_RECEIVE_SOURCES:
			return {
				...state,
				sources: action.sources,
			};
		case WPC_CREATE_EXPORT:
			return {
				...state,
				isCreating: true,
				createdExport: null,
			};
		case WPC_FINISH_EXPORT:
			return {
				...state,
				isCreating: false,
				createdExport: action.result,
			};
		case WPC_RECEIVE_APP_PASSWORD:
			return {
				...state,
				introspection: action.introspection,
			};
		case WPC_DELETE_CREDENTIALS:
			return {
				...state,
				isDeleting: true,
			};
		case WPC_DELETE_CREDENTIALS_SUCCESS: {
			return {
				...state,
				isDeleting: false,
			};
		}
		default:
			return state;
	}
}

const IMPORT_DEFAULT_STATE = {
	isValidating: false,
	validatedExport: null,
	exportData: null,
	source: '',
	sources: [],
	userMap: {},
	roleMap: {},
};

function importReducer( state = IMPORT_DEFAULT_STATE, action ) {
	switch ( action.type ) {
		case VALIDATE_EXPORT_FILE:
		case VALIDATE_EXPORT_DATA:
			return {
				...state,
				isValidating: true,
				validatedExport: null,
				exportData: null,
				source: action.source,
			};
		case VALIDATED_EXPORT:
			const nextState = {
				...state,
				isValidating: false,
				validatedExport: action.result,
			};

			if ( action.result.isSuccess() ) {
				nextState.exportData = action.result.data;
			}

			return nextState;
		case EDIT_IMPORT_SOURCES:
			return {
				...state,
				sources: action.sources,
			};
		case EDIT_IMPORT_USER_MAP:
			return {
				...state,
				userMap: {
					...action.map,
				},
			};
		case EDIT_IMPORT_ROLE_MAP:
			return {
				...state,
				roleMap: {
					...action.map,
				},
			};
		case IMPORT_RESET:
			return {
				...IMPORT_DEFAULT_STATE,
			};
		default:
			return state;
	}
}

export default combineReducers( {
	exports(
		state = {
			items: null,
			errors: {},
		},
		action
	) {
		switch ( action.type ) {
			case RECEIVE_EXPORTS:
				return {
					...state,
					items: [ ...action.exports ],
				};
			case FINISH_EXPORT:
				if ( action.result.isSuccess() && state.items ) {
					return {
						...state,
						items: [ action.result.data, ...state.items ],
					};
				}

				return state;
			case DELETE_EXPORT:
				return {
					...state,
					items: state.items.filter(
						( item ) => item.id !== action.id
					),
				};
			case SET_EXPORT_ERROR:
				return {
					...state,
					errors: {
						...state.errors,
						[ action.id ]: action.error,
					},
				};
			default:
				return state;
		}
	},
	creating(
		state = {
			inProgress: false,
			lastResult: null,
		},
		action
	) {
		switch ( action.type ) {
			case CREATE_EXPORT:
				return {
					...state,
					inProgress: true,
					lastResult: null,
				};
			case FINISH_EXPORT:
				return {
					...state,
					inProgress: false,
					lastResult: action.result,
				};
			default:
				return state;
		}
	},
	sources( state = null, action ) {
		if ( action.type === RECEIVE_SOURCES ) {
			return [ ...action.sources ];
		}

		return state;
	},
	import: importReducer,
	wpConnect,
	credentials( state = null, action ) {
		switch ( action.type ) {
			case WPC_RESET:
				return null;
			case WPC_APPLICATION_APPROVED:
				return action.credentials;
			default:
				return state;
		}
	},
} );
