/**
 * WordPress dependencies
 */
import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { useNavigateTo } from '@ithemes/security.pages.settings';
import { STORE_NAME } from '@ithemes/security.import-export.data';

export function useWpConnectAuthGuard( { allowLax = false } = {} ) {
	const navigateTo = useNavigateTo();
	const { credentials, hasExports } = useSelect(
		( select ) => ( {
			credentials: select( STORE_NAME ).getWpConnectCredentials(),
			hasExports: select( STORE_NAME ).hasWpConnectExports(),
		} ),
		[]
	);
	useEffect( () => {
		if ( ! credentials && ! ( hasExports && allowLax ) ) {
			navigateTo( '/import/select-export/wordpress-connect' );
		}
	}, [ credentials, hasExports ] );
}
