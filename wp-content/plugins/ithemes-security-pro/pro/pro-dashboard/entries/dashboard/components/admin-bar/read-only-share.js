/**
 * External dependencies
 */
import { get } from 'lodash';

/**
 * WordPress dependencies
 */
import { Tooltip } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { isApiError } from '@ithemes/security-utils';
import { getAvatarUrl } from '@ithemes/security.dashboard.dashboard';

export default function ReadOnlyShare( { dashboardId } ) {
	const { dashboard } = useSelect(
		( select ) => ( {
			dashboard: select( 'ithemes-security/dashboard' ).getDashboard(
				dashboardId
			),
		} ),
		[ dashboardId ]
	);
	const author = get( dashboard, [ '_embedded', 'author', 0 ] );

	if ( ! author || isApiError( author ) ) {
		return null;
	}

	return (
		<div className="itsec-admin-bar__share">
			{ author && ! isApiError( author ) && (
				<div className="itsec-admin-bar-share__owner">
					<Tooltip text={ author.name }>
						<span className="itsec-admin-bar-share__recipient">
							<img
								className="itsec-admin-bar-share__user-avatar"
								src={ getAvatarUrl( author ) }
								alt=""
							/>
						</span>
					</Tooltip>
				</div>
			) }
		</div>
	);
}
