/**
 * External dependencies
 */
import { uniqueId, isArray } from 'lodash';

/**
 * WordPress dependencies
 */
import { __, _x } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';
import { useCallback, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { Result, transformApiErrorToList } from '@ithemes/security-utils';
import { useAsync } from '@ithemes/security-hocs';

export function getTwoFactor( twoFactor ) {
	switch ( twoFactor ) {
		case 'enabled':
			return [ 'yes', __( 'Enabled', 'it-l10n-ithemes-security-pro' ) ];
		case 'not-enabled':
			return [ 'no-alt', __( 'Not Enabled', 'it-l10n-ithemes-security-pro' ) ];
		case 'not-available':
			return [ 'no-alt', __( 'Module Inactive', 'it-l10n-ithemes-security-pro' ) ];
		case 'enforced-not-configured':
			return [ 'minus', __( 'Enforced, Not Configured', 'it-l10n-ithemes-security-pro' ) ];
		default:
			return [ 'minus', twoFactor ];
	}
}

export function getPasswordStrength( strength ) {
	switch ( strength ) {
		case 0:
		case 1:
			return [ 'short', _x( 'Very Weak', 'password strength', 'it-l10n-ithemes-security-pro' ) ];
		case 2:
			return [ 'bad', _x( 'Weak', 'password strength', 'it-l10n-ithemes-security-pro' ) ];
		case 3:
			return [ 'good', _x( 'Medium', 'password strength', 'it-l10n-ithemes-security-pro' ) ];
		case 4:
			return [ 'strong', _x( 'Strong', 'password strength', 'it-l10n-ithemes-security-pro' ) ];
		default:
			return [ 'unknown', _x( 'Unknown', 'password strength', 'it-l10n-ithemes-security-pro' ) ];
	}
}

export function useCardLink( link, onComplete, immediate = true ) {
	const { execute, status, value, error } = useAsync(
		useCallback(
			( data ) =>
				apiFetch( {
					url: link?.href,
					method: isArray( link?.methods )
						? link?.methods[ 0 ]
						: link?.methods,
					parse: false,
					data,
				} )
					.then( ( response ) => {
						onComplete?.( response );

						return Result.fromResponse( response );
					} )
					.catch(
						async ( response ) =>
							throw ( await Result.fromResponse( response ) )
					),
			[ link ]
		),
		immediate
	);
	useNoticeCreator( status, value, error );

	return { execute, status, value, error };
}

/**
 * Creates notices from the async response.
 *
 * @param {string} status
 * @param {Result} value
 * @param {Result} error
 */
export function useNoticeCreator( status, value, error ) {
	const { createNotice, removeNotice } = useDispatch( 'core/notices' );

	const autoDismiss = ( type, message ) => {
		const id = uniqueId( 'security-profile-action' );
		createNotice( type, message, { context: 'ithemes-security', id } );
		setTimeout( () => removeNotice( id, 'ithemes-security' ), 5000 );
	};

	useEffect( () => {
		if ( status === 'error' ) {
			const message = transformApiErrorToList( error.error ).join( ' ' );
			createNotice(
				'error',
				message || __( 'An unknown error occurred.', 'it-l10n-ithemes-security-pro' ),
				{ context: 'ithemes-security' }
			);
		} else if ( status === 'success' ) {
			if ( value.success.length > 0 ) {
				autoDismiss( 'success', value.success.join( ' ' ) );
			}

			if ( value.info.length > 0 ) {
				autoDismiss( 'info', value.info.join( ' ' ) );
			}

			if ( value.warning.length > 0 ) {
				autoDismiss( 'warning', value.warning.join( ' ' ) );
			}
		}
	}, [ status, value, error ] );
}
