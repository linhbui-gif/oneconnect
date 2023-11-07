/**
 * WordPress dependencies
 */
import { createRegistryControl } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { controls as defaultControls } from '@ithemes/security.packages.data';
import { Result, WPError } from '@ithemes/security-utils';
import { STORE_NAME } from './';

export function wpConnectFetch( request ) {
	return {
		type: 'WP_CONNECT_FETCH',
		request,
	};
}

export function wpConnectFetchResult( request ) {
	return {
		type: 'WP_CONNECT_FETCH_RESULT',
		request,
	};
}

function makeFetchRequest( registry, { url, path, headers, ...request } ) {
	const creds = registry.select( STORE_NAME ).getWpConnectCredentials();
	url =
		url ||
		registry.select( STORE_NAME ).getWpConnectRestApiEndpoint( path );

	if ( ! url ) {
		throw {
			code: 'not_connected',
			message: __(
				'iThemes Security is not connected to a site to fetch exports for.',
				'LION'
			),
		};
	}

	if ( ! creds ) {
		throw {
			code: 'not_authenticated',
			message: __(
				'iThemes Security has not authenticated with the site to fetch exports for.',
				'LION'
			),
		};
	}

	return {
		url,
		headers: {
			Authorization: `Basic ${ window.btoa(
				creds.username + ':' + creds.password
			) }`,
			...( headers || {} ),
		},
		credentials: 'omit',
		...request,
	};
}

const controls = {
	WP_CONNECT_FETCH: createRegistryControl(
		( registry ) => ( { request } ) => {
			try {
				return defaultControls.API_FETCH( {
					request: makeFetchRequest( registry, request ),
				} );
			} catch ( e ) {
				return Promise.reject( e );
			}
		}
	),
	WP_CONNECT_FETCH_RESULT: createRegistryControl(
		( registry ) => ( { request } ) => {
			try {
				return defaultControls.API_FETCH_RESULT( {
					request: makeFetchRequest( registry, request ),
				} );
			} catch ( e ) {
				return new Result(
					Result.ERROR,
					new WPError( e.code, e.message )
				);
			}
		}
	),
};

export default controls;
