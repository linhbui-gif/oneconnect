/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { decodeEntities } from '@wordpress/html-entities';
import { __ } from '@wordpress/i18n';
import { Dropdown, Button, Dashicon } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { AdminBarFill } from '@ithemes/security.dashboard.api';
import Share from './share';
import ReadOnlyShare from './read-only-share';
import ManageDashboards from '../manage-dashboards';
import './style.scss';

export default function AdminBar() {
	const {
		canEdit,
		canCreate,
		dashboards,
		dashboard,
		dashboardId,
	} = useSelect( ( select ) => {
		const _dashboardId = select(
			'ithemes-security/dashboard'
		).getViewingDashboardId();

		return {
			dashboardId: _dashboardId,
			canEdit: select( 'ithemes-security/dashboard' ).canEditDashboard(
				_dashboardId
			),
			dashboard: select( 'ithemes-security/dashboard' ).getDashboard(
				_dashboardId
			),
			canCreate: select(
				'ithemes-security/dashboard'
			).canCreateDashboards(),
			dashboards: select(
				'ithemes-security/dashboard'
			).getAvailableDashboards(),
		};
	}, [] );
	const title = (
		<h1>
			{ dashboard
				? decodeEntities( dashboard.label.rendered )
				: __( 'No Dashboard Selected', 'it-l10n-ithemes-security-pro' ) }
		</h1>
	);

	return (
		<AdminBarFill type="primary">
			<div className="itsec-admin-bar__title">
				{ ! canCreate && dashboards.length <= 1 && dashboardId ? (
					title
				) : (
					<Dropdown
						className="itsec-admin-bar-manage-dashboards__trigger"
						contentClassName="itsec-admin-bar-manage-dashboards__content"
						position="bottom right"
						headerTitle={ __( 'Manage Dashboards', 'it-l10n-ithemes-security-pro' ) }
						expandOnMobile
						focusOnMount="container"
						renderToggle={ ( { isOpen, onToggle } ) => (
							<Button
								aria-expanded={ isOpen }
								onClick={ onToggle }
								variant="secondary"
							>
								{ title }
								<Dashicon
									icon={
										isOpen
											? 'arrow-up-alt2'
											: 'arrow-down-alt2'
									}
									size={ 15 }
								/>
							</Button>
						) }
						renderContent={ ( { onClose } ) => (
							<ManageDashboards
								dashboardId={ dashboardId }
								close={ onClose }
							/>
						) }
					/>
				) }
			</div>
			{ dashboard &&
				( canEdit ? (
					<Share dashboardId={ dashboardId } />
				) : (
					<ReadOnlyShare dashboardId={ dashboardId } />
				) ) }
		</AdminBarFill>
	);
}
