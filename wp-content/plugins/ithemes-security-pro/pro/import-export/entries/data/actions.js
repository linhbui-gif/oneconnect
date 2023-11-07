/**
 * External dependencies
 */
import { map, get, find, difference } from 'lodash';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import {
	apiFetch,
	apiFetchResult,
	createNotice,
	select,
	dispatch,
	MODULES_STORE_NAME,
} from '@ithemes/security.packages.data';
import { wpConnectFetch, wpConnectFetchResult } from './controls';
import { STORE_NAME } from './';

export function receiveSources( sources ) {
	return {
		type: RECEIVE_SOURCES,
		sources,
	};
}

export function receiveExports( exports ) {
	return {
		type: RECEIVE_EXPORTS,
		exports,
	};
}

export function* createExport( data ) {
	yield { type: CREATE_EXPORT, export: data };
	const result = yield apiFetchResult( {
		path: '/ithemes-security/v1/import-export/exports',
		method: 'POST',
		data,
	} );
	yield { type: FINISH_EXPORT, result };

	return result;
}

export function* deleteExport( id ) {
	yield apiFetch( {
		path: `/ithemes-security/v1/import-export/exports/${ id }`,
		method: 'DELETE',
	} );
	yield { type: DELETE_EXPORT, id };
}

export function* deleteExports( ids ) {
	let fetched;

	try {
		fetched = yield apiFetch( {
			path: '/batch/v1',
			method: 'POST',
			data: {
				requests: ids.map( ( id ) => ( {
					method: 'DELETE',
					path: `/ithemes-security/v1/import-export/exports/${ id }`,
				} ) ),
			},
		} );
	} catch ( e ) {
		yield createNotice(
			'error',
			e.message || __( 'Could not delete exports.', 'it-l10n-ithemes-security-pro' )
		);
		return e;
	}

	for ( let i = 0; i < ids.length; i++ ) {
		const response = fetched.responses[ i ];

		if ( response.status >= 400 ) {
			yield {
				type: SET_EXPORT_ERROR,
				id: ids[ i ],
				error: response.body,
			};
		} else {
			yield { type: DELETE_EXPORT, id: ids[ i ] };
		}
	}
}

export function* validateExportFile( file, source = 'upload' ) {
	yield { type: VALIDATE_EXPORT_FILE, file, source };

	const formData = new window.FormData();
	formData.append( 'file', file );

	const result = yield apiFetchResult( {
		path: 'ithemes-security/rpc/import/validate',
		method: 'POST',
		body: formData,
	} );
	yield { type: VALIDATED_EXPORT, result };
}

export function* validateExportData( data, source = 'connect' ) {
	yield { type: VALIDATE_EXPORT_DATA, data, source };
	const result = yield apiFetchResult( {
		path: 'ithemes-security/rpc/import/validate',
		method: 'POST',
		data,
	} );
	yield { type: VALIDATED_EXPORT, result };
}

export function* wpConnectReset() {
	yield dispatch( STORE_NAME, 'wpConnectDeleteCredentials' );
	yield { type: WPC_RESET };
}

export function wpConnectEnterWebsite( url ) {
	return {
		type: WPC_ENTER_WEBSITE,
		url,
	};
}

function discover( url ) {
	return apiFetchResult( {
		path: '/ithemes-security/rpc/discover',
		method: 'POST',
		data: { url },
	} );
}

export function* wpConnectDiscoverRestApi( url ) {
	yield { type: WPC_DISCOVER_REST_API, url };
	const result = yield discover( url );
	yield { type: WPC_DISCOVERED_REST_API, result };

	return result;
}

export function* wpConnectApplicationApproved( url, credentials ) {
	yield {
		type: WPC_APPLICATION_APPROVED,
		url,
		credentials,
	};
	const result = yield discover( url );
	const hasSupport =
		result.isSuccess() &&
		result.data.itsec.routes.hasOwnProperty(
			'/ithemes-security/v1/import-export/exports'
		);
	yield { type: WPC_CHECK_SUPPORT, discovery: result, hasSupport };
}

export function wpConnectApplicationRejected() {
	return {
		type: WPC_APPLICATION_REJECTED,
	};
}

export function wpConnectReceiveAppPassword( introspection ) {
	return {
		type: WPC_RECEIVE_APP_PASSWORD,
		introspection,
	};
}

export function wpConnectReceiveExports( exports ) {
	return {
		type: WPC_RECEIVE_EXPORTS,
		exports,
	};
}

export function wpConnectReceiveSources( sources ) {
	return {
		type: WPC_RECEIVE_SOURCES,
		sources,
	};
}

export function* wpConnectCreateExport( data ) {
	yield { type: WPC_CREATE_EXPORT, export: data };
	const result = yield wpConnectFetchResult( {
		data,
		path: '/ithemes-security/v1/import-export/exports',
		method: 'POST',
	} );
	yield { type: WPC_FINISH_EXPORT, result };

	return result;
}

export function* wpConnectDeleteCredentials() {
	if ( ! ( yield select( STORE_NAME, 'getWpConnectCredentials' ) ) ) {
		return;
	}

	yield { type: WPC_DELETE_CREDENTIALS };
	const introspection = yield select(
		STORE_NAME,
		'getWpConnectCredentialsIntrospection'
	);
	const link = get( introspection, [ '_links', 'self', 0, 'href' ] );

	if ( link ) {
		try {
			yield wpConnectFetch( {
				url: link,
				method: 'DELETE',
			} );
		} catch ( e ) {
			// For now we just ignore this error since the user can't recover from this.
			// eslint-disable-next-line no-console
			console.error( e );
		}
	}

	yield { type: WPC_DELETE_CREDENTIALS_SUCCESS };
}

export function editImportSources( sources ) {
	return {
		type: EDIT_IMPORT_SOURCES,
		sources,
	};
}

export function editImportUserMap( userMap ) {
	return {
		type: EDIT_IMPORT_USER_MAP,
		map: userMap,
	};
}

export function editImportRoleMap( roleMap ) {
	return {
		type: EDIT_IMPORT_ROLE_MAP,
		map: roleMap,
	};
}

export function* transformImportExportData() {
	const exportData = yield select( STORE_NAME, 'getImportExportData' );
	const sources = yield select( STORE_NAME, 'getImportSources' );
	const userMap = yield select( STORE_NAME, 'getImportUserMap' );
	const roleMap = yield select( STORE_NAME, 'getImportRoleMap' );

	return yield apiFetch( {
		method: 'POST',
		path: '/ithemes-security/rpc/import/transform',
		data: {
			export: exportData,
			sources,
			user_map: userMap,
			role_map: roleMap,
		},
	} );
}

export function* applyExportData() {
	yield dispatch( MODULES_STORE_NAME, 'resetModuleEdits' );
	yield dispatch( MODULES_STORE_NAME, 'resetSettingEdits' );
	yield dispatch( 'ithemes-security/user-groups-editor', 'resetAllEdits' );
	const exportData = yield* transformImportExportData();
	const sources = yield select( STORE_NAME, 'getImportSources' );

	for ( const source of sources ) {
		if ( ! exportData.sources[ source ] ) {
			continue;
		}

		const data = exportData.sources[ source ];

		switch ( source ) {
			case 'modules':
				for ( const [ module, status ] of Object.entries( data ) ) {
					yield dispatch( MODULES_STORE_NAME, 'editModule', module, {
						status: {
							selected: status,
						},
					} );
				}
				break;
			case 'settings':
				for ( const [ module, settings ] of Object.entries( data ) ) {
					yield dispatch(
						MODULES_STORE_NAME,
						'editSettings',
						module,
						settings
					);
				}
				break;
			case 'user-groups':
				const definitions = yield select(
					'ithemes-security/user-groups',
					'getSettingDefinitions',
					null,
					undefined,
					{ skipConditions: true }
				);
				const current = yield select(
					'ithemes-security/user-groups',
					'getMatchables'
				);

				for ( const group of current ) {
					if (
						group.type === 'user-group' &&
						! find( data, { id: group.id } )
					) {
						yield dispatch(
							'ithemes-security/user-groups-editor',
							'markGroupForDeletion',
							group.id
						);
					}
				}

				for ( const userGroup of data ) {
					if ( ! find( current, { id: userGroup.id } ) ) {
						const created = yield dispatch(
							'ithemes-security/user-groups-editor',
							'createLocalGroup',
							userGroup.id
						);

						if ( ! created ) {
							continue;
						}
					}

					yield dispatch(
						'ithemes-security/user-groups-editor',
						'editGroup',
						userGroup.id,
						{
							...userGroup,
							roles: map( userGroup.roles, 'slug' ),
							users: map( userGroup.users, 'id' ),
						}
					);

					for ( const module of definitions ) {
						if ( ! exportData.sources.settings[ module.id ] ) {
							continue;
						}

						for ( const setting of Object.keys(
							module.settings
						) ) {
							const value = get(
								exportData.sources.settings[ module.id ],
								setting
							);

							if ( ! Array.isArray( value ) ) {
								continue;
							}

							yield dispatch(
								'ithemes-security/user-groups-editor',
								'editGroupSetting',
								userGroup.id,
								module.id,
								setting,
								value.includes( userGroup.id )
							);
						}
					}
				}
				break;
		}
	}
}

export function* completeImport() {
	const exportData = yield select( STORE_NAME, 'getImportExportData' );
	const sources = yield select( STORE_NAME, 'getImportSources' );
	const userMap = yield select( STORE_NAME, 'getImportUserMap' );
	const roleMap = yield select( STORE_NAME, 'getImportRoleMap' );

	return yield apiFetchResult( {
		path: '/ithemes-security/rpc/import/run',
		method: 'POST',
		data: {
			export: exportData,
			user_map: userMap,
			role_map: roleMap,
			sources: difference( sources, [
				// Exclude sources that are processed interactively.
				'modules',
				'settings',
				'user-groups',
			] ),
		},
	} );
}

export function resetImport() {
	return {
		type: IMPORT_RESET,
	};
}

export const RECEIVE_SOURCES = 'RECEIVE_SOURCES';

export const RECEIVE_EXPORTS = 'RECEIVE_EXPORTS';
export const CREATE_EXPORT = 'CREATE_EXPORT';
export const FINISH_EXPORT = 'FINISH_EXPORT';
export const DELETE_EXPORT = 'DELETE_EXPORT';
export const SET_EXPORT_ERROR = 'SET_EXPORT_ERROR';

export const VALIDATE_EXPORT_FILE = 'VALIDATE_EXPORT_FILE';
export const VALIDATE_EXPORT_DATA = 'VALIDATE_EXPORT_DATA';
export const VALIDATED_EXPORT = 'VALIDATED_EXPORT';
export const EDIT_IMPORT_SOURCES = 'EDIT_IMPORT_SOURCES';
export const EDIT_IMPORT_USER_MAP = 'EDIT_IMPORT_USER_MAP';
export const EDIT_IMPORT_ROLE_MAP = 'EDIT_IMPORT_ROLE_MAP';
export const IMPORT_RESET = 'IMPORT_RESET';

// wpConnect
export const WPC_RESET = 'WPC_RESET';
export const WPC_ENTER_WEBSITE = 'WPC_ENTER_WEBSITE';
export const WPC_DISCOVER_REST_API = 'WPC_DISCOVER_REST_API';
export const WPC_DISCOVERED_REST_API = 'WPC_DISCOVERED_REST_API';
export const WPC_APPLICATION_REJECTED = 'WPC_APPLICATION_REJECTED';
export const WPC_APPLICATION_APPROVED = 'WPC_APPLICATION_APPROVED';
export const WPC_CHECK_SUPPORT = 'WPC_CHECK_SUPPORT';
export const WPC_RECEIVE_EXPORTS = 'WPC_RECEIVE_EXPORTS';
export const WPC_RECEIVE_SOURCES = 'WPC_RECEIVE_SOURCES';
export const WPC_CREATE_EXPORT = 'WPC_CREATE_EXPORT';
export const WPC_FINISH_EXPORT = 'WPC_FINISH_EXPORT';
export const WPC_RECEIVE_APP_PASSWORD = 'WPC_RECEIVE_APP_PASSWORD';
export const WPC_DELETE_CREDENTIALS = 'WPC_DELETE_CREDENTIALS';
export const WPC_DELETE_CREDENTIALS_SUCCESS = 'WPC_DELETE_CREDENTIALS_SUCCESS';
