/**
 * External dependencies
 */
import { get, uniqBy } from 'lodash';
import { get as getWild } from 'get-wild';
import createSelector from 'rememo';

const EMPTY_LIST = [];

export function getSources( state ) {
	return state.sources || EMPTY_LIST;
}

export function getExports( state ) {
	return state.exports.items || EMPTY_LIST;
}

export function isCreatingExport( state ) {
	return state.creating.inProgress;
}

export function getLastCreatedExportResult( state ) {
	return state.creating.lastResult;
}

export function getImportExportData( state ) {
	return state.import.exportData;
}

export function getImportExportValidationResult( state ) {
	return state.import.validatedExport;
}

export function getImportExportSource( state ) {
	return state.import.source;
}

export function getImportSources( state ) {
	return state.import.sources;
}

export function getImportUserMap( state ) {
	return state.import.userMap;
}

export function getImportRoleMap( state ) {
	return state.import.roleMap;
}

function getReplacementTargets( sources, importSources, exportData, type ) {
	const targets = [];

	for ( const source of sources ) {
		if ( ! importSources.includes( source.slug ) ) {
			continue;
		}

		const map = source.transform_map[ type ];

		if ( ! map || ! exportData.sources[ source.slug ] ) {
			continue;
		}

		for ( const path of map ) {
			targets.push(
				...getWild( exportData.sources[ source.slug ], path )
			);
		}
	}

	if ( type === 'users' ) {
		return uniqBy( targets, 'id' );
	}

	if ( type === 'roles' ) {
		return uniqBy( targets, 'slug' );
	}

	return targets;
}

/**
 * Gets users from an export that should be replaced.
 *
 * @type {(function(): Array<{id: number, email: string, name: string}>)}
 */
export const getImportExportUserReplacementTargets = createSelector(
	( state ) =>
		getReplacementTargets(
			getSources( state ),
			getImportSources( state ),
			state.import.exportData,
			'users'
		),
	( state ) => [
		getSources( state ),
		getImportSources( state ),
		state.import.exportData,
	]
);

/**
 * Gets roles from an export that should be replaced.
 *
 * @type {(function(): Array<{slug: string, label: string}>)}
 */
export const getImportExportRoleReplacementTargets = createSelector(
	( state ) =>
		getReplacementTargets(
			getSources( state ),
			getImportSources( state ),
			state.import.exportData,
			'roles'
		),
	( state ) => [
		getSources( state ),
		getImportSources( state ),
		state.import.exportData,
	]
);

export function getWpConnectStep( state ) {
	return state.wpConnect.step;
}

export function getWpConnectSiteUrl( state ) {
	return state.wpConnect.siteUrl;
}

export function getWpConnectExports( state ) {
	return state.wpConnect.exports || EMPTY_LIST;
}

export function hasWpConnectExports( state ) {
	return getWpConnectExports( state ).length > 0;
}

export function getWpConnectSources( state ) {
	return state.wpConnect.sources || EMPTY_LIST;
}

export function wpConnectIsCreating( state ) {
	return state.wpConnect.isCreating;
}

export function getWpConnectExport( state ) {
	return state.wpConnect.createdExport;
}

export function isDeletingWpConnectCredential( state ) {
	return state.wpConnect.isDeleting && !! getWpConnectCredentials( state );
}

export function getWpConnectCredentials( state ) {
	return state.credentials;
}

export function areWpConnectCredentialsExpired( state ) {
	return (
		! getWpConnectCredentials( state ) &&
		[ 'appAuthed', 'hasSupport', 'noSupport' ].includes(
			getWpConnectStep( state )
		)
	);
}

export function getWpConnectDiscoveryResult( state ) {
	return state.wpConnect.discovery;
}

export function getWpConnectCredentialsIntrospection( state ) {
	return state.wpConnect.introspection;
}

export function getWpConnectRestApiEndpoint( state, endpoint ) {
	const link = get( state, [
		'wpConnect',
		'discovery',
		'data',
		'itsec',
		'routes',
		endpoint,
		'_links',
		'self',
		0,
		'href',
	] );

	if ( link ) {
		return link;
	}

	const apiRoot = get(
		state,
		[ 'wpConnect', 'discovery', 'data', 'url' ],
		''
	);

	if ( ! apiRoot.length ) {
		return undefined;
	}

	let path = endpoint;

	if ( -1 !== apiRoot.indexOf( '?' ) ) {
		path = path.replace( '?', '&' );
	}

	path = path.replace( /^\//, '' );

	// API root may already include query parameter prefix if site is
	// configured to use plain permalinks.
	if ( -1 !== apiRoot.indexOf( '?' ) ) {
		path = path.replace( '?', '&' );
	}

	return apiRoot + path;
}

export function getWpConnectAppPasswordsAuthEndpoint( state ) {
	return get( state, [
		'wpConnect',
		'discovery',
		'data',
		'index',
		'authentication',
		'application-passwords',
		'endpoints',
		'authorization',
	] );
}
