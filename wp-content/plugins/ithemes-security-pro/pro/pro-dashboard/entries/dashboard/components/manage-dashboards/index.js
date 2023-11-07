/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

/**
 * Internal dependencies
 */
import Dashboard from './dashboard';
import './style.scss';

export default function ManageDashboards( { close } ) {
	const { currentUserId, canCreate, dashboards } = useSelect(
		( select ) => ( {
			canCreate: select(
				'ithemes-security/dashboard'
			).canCreateDashboards(),
			dashboards: select(
				'ithemes-security/dashboard'
			).getAvailableDashboards(),
			currentUserId:
				select( 'ithemes-security/core' ).getCurrentUser()?.id || 0,
		} ),
		[]
	);
	const { viewCreateDashboard } = useDispatch( 'ithemes-security/dashboard' );

	return (
		<div className="itsec-manage-dashboards">
			<header className="itsec-manage-dashboards__header">
				<h3>{ __( 'Manage Dashboards', 'it-l10n-ithemes-security-pro' ) }</h3>
				<p>
					{ __(
						'Switch, manage, or create new dashboards.',
						'it-l10n-ithemes-security-pro'
					) }
				</p>
			</header>
			<ul className="itsec-manage-dashboards__list">
				{ dashboards.map( ( dashboard ) => (
					<Dashboard
						key={ dashboard.id }
						dashboard={ dashboard }
						currentUserId={ currentUserId }
						close={ close }
					/>
				) ) }
			</ul>
			{ canCreate && (
				<section className="itsec-manage-dashboards__create">
					<Button
						variant="link"
						onClick={ () => [ viewCreateDashboard(), close() ] }
					>
						{ __( 'Create New Dashboard', 'it-l10n-ithemes-security-pro' ) }
					</Button>
				</section>
			) }
		</div>
	);
}
