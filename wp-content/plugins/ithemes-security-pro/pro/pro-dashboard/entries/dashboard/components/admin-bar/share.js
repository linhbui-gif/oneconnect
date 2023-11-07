/**
 * External dependencies
 */
import { get, find } from 'lodash';

/**
 * WordPress dependencies
 */
import { Tooltip, Dropdown, Button } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { getAvatarUrl } from '@ithemes/security.dashboard.dashboard';
import ShareUser from './share-user';
import ShareRole from './share-role';
import ShareDashboard from '../share-dashboard';

export default function Share( { dashboardId } ) {
	const { dashboard } = useSelect(
		( select ) => ( {
			dashboard: select(
				'ithemes-security/dashboard'
			).getDashboardForEdit( dashboardId ),
		} ),
		[ dashboardId ]
	);
	const author = get( dashboard, [ '_embedded', 'author', 0 ] );
	const getUser = ( id ) =>
		find(
			get(
				dashboard,
				[ '_embedded', 'ithemes-security:shared-with' ],
				[]
			),
			{ id }
		);

	return (
		<div className="itsec-admin-bar__share">
			{ author && (
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
			<div className="itsec-admin-bar-share__recipients">
				{ get( dashboard, 'sharing', [] )
					.filter( ( share ) => share.type === 'user' )
					.map( ( share ) =>
						share.users.map( ( userId ) => (
							<ShareUser
								share={ share }
								userId={ userId }
								user={ getUser( userId ) }
								dashboardId={ dashboardId }
								key={ userId }
							/>
						) )
					) }
				{ get( dashboard, 'sharing', [] )
					.filter( ( share ) => share.type === 'role' )
					.map( ( share ) =>
						share.roles.map( ( role ) => (
							<ShareRole
								share={ share }
								role={ role }
								dashboardId={ dashboardId }
								key={ role }
							/>
						) )
					) }
			</div>
			<div className="itsec-admin-bar-share__add-share-container">
				<Dropdown
					className="itsec-admin-bar-share__add-share"
					contentClassName="itsec-admin-bar-share__add-share-content"
					position="bottom"
					expandOnMobile
					focusOnMount="container"
					headerTitle={ __( 'Share with User', 'it-l10n-ithemes-security-pro' ) }
					renderToggle={ ( { isOpen, onToggle } ) => (
						<Button
							label={ __( 'Share Dashboard', 'it-l10n-ithemes-security-pro' ) }
							aria-pressed={ isOpen }
							onClick={ onToggle }
							icon="plus-alt"
							size={ 40 }
						/>
					) }
					renderContent={ ( { onClose } ) => (
						<ShareDashboard
							dashboardId={ dashboardId }
							close={ onClose }
						/>
					) }
				/>
			</div>
		</div>
	);
}
