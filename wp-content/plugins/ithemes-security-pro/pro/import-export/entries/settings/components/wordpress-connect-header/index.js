/**
 * External dependencies
 */
import { get, last, sortBy } from 'lodash';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { Spinner } from '@ithemes/security-components';
import { STORE_NAME } from '@ithemes/security.import-export.data';
import './style.scss';

export default function ConnectingToHeader( { showSpinner, isConnected } ) {
	const { url, discovery } = useSelect(
		( select ) => ( {
			url: select( STORE_NAME ).getWpConnectSiteUrl(),
			discovery: select( STORE_NAME ).getWpConnectDiscoveryResult(),
		} ),
		[]
	);
	const home = get( discovery, [ 'data', 'index', 'home' ] );
	const name = get( discovery, [ 'data', 'index', 'name' ] );
	const description = get( discovery, [ 'data', 'index', 'description' ] );
	const image = get( discovery, [
		'data',
		'index',
		'_embedded',
		'wp:featuredmedia',
		0,
	] );
	const imageSize = image && getClosestSize( image, { width: 60 } );
	let title;
	if ( isConnected ) {
		/* translators: 1. Site name. */
		title = __( 'Connected to %s', 'it-l10n-ithemes-security-pro' );
	} else {
		/* translators: 1. Site name. */
		title = __( 'Connecting to %s', 'it-l10n-ithemes-security-pro' );
	}

	return (
		<div className="itsec-import-wordpress-connect-header">
			{ imageSize && (
				<img
					src={ imageSize.source_url }
					height={ imageSize.height }
					width={ imageSize.width }
					alt={ image.alt_text }
				/>
			) }
			<div className="itsec-import-wordpress-connect-header__details">
				<h3>{ sprintf( title, home || url ) }</h3>
				{ name && (
					<span className="itsec-import-wordpress-connect-header__name">
						{ decodeEntities( name ) }
					</span>
				) }
				{ description && (
					<span className="itsec-import-wordpress-connect-header__description">
						{ decodeEntities( description ) }
					</span>
				) }
			</div>
			{ showSpinner && <Spinner color="--itsec-primary-theme-color" /> }
		</div>
	);
}

function getClosestSize( image, match ) {
	const property = match.width ? 'width' : 'height';
	const sorted = sortBy( image.media_details.sizes, property );

	for ( const size of sorted ) {
		if ( size[ property ] > match[ property ] ) {
			return size;
		}
	}

	if ( sorted.length ) {
		return last( sorted );
	}

	return {
		height: image.media_details.height || match.height,
		width: image.media_details.width || match.width,
		source_url: image.source_url,
	};
}
