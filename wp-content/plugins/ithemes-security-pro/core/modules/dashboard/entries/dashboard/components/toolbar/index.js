/**
 * WordPress dependencies
 */
import { Popover, Toolbar, ToolbarButton } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { AdminBarSlot } from '@ithemes/security.dashboard.api';
import { useGlobalNavigationUrl } from '@ithemes/security-utils';
import { useCanWrite, useConfigContext } from '../../utils';
import EditCards from '../edit-cards';
import './style.scss';

export default function( { dashboardId } ) {
	const canWrite = useCanWrite();
	const settingsUrl = useGlobalNavigationUrl( 'settings' );
	const { canManage } = useConfigContext();
	const { canCreate, canEdit, editingCards } = useSelect(
		( select ) => ( {
			canCreate: select(
				'ithemes-security/dashboard'
			).canCreateDashboards(),
			canEdit: select( 'ithemes-security/dashboard' ).canEditDashboard(
				dashboardId
			),
			editingCards: select(
				'ithemes-security/dashboard'
			).isEditingCards(),
		} ),
		[ dashboardId ]
	);
	const { openEditCards, closeEditCards } = useDispatch(
		'ithemes-security/dashboard'
	);

	if ( ! canWrite && ! canManage ) {
		return null;
	}

	return (
		<div className="itsec-dashboard-toolbar">
			<Toolbar label={ __( 'Dashboard Toolbar', 'it-l10n-ithemes-security-pro' ) }>
				{ canManage && (
					<ToolbarButton
						text={ __( 'Settings', 'it-l10n-ithemes-security-pro' ) }
						icon="admin-settings"
						href={ settingsUrl }
					/>
				) }
				<AdminBarSlot />
				{ canEdit && (
					<>
						<ToolbarButton
							text={ __( 'Edit Cards', 'it-l10n-ithemes-security-pro' ) }
							icon="layout"
							className="itsec-admin-bar-edit-cards__trigger"
							aria-expanded={ editingCards }
							onClick={
								editingCards ? closeEditCards : openEditCards
							}
						/>
						{ editingCards && (
							<Popover
								className="itsec-admin-bar-edit-cards__content"
								position="bottom"
								headerTitle={ __( 'Edit Cards', 'it-l10n-ithemes-security-pro' ) }
								expandOnMobile
								onFocusOutside={ closeEditCards }
								onClose={ closeEditCards }
								focusOnMount="container"
							>
								<EditCards
									dashboardId={ dashboardId }
									close={ closeEditCards }
								/>
							</Popover>
						) }
					</>
				) }
				{ ( canEdit || canCreate ) && <Help /> }
			</Toolbar>
		</div>
	);
}

function Help() {
	const { page } = useSelect( ( select ) => ( {
		page: select( 'ithemes-security/dashboard' ).getCurrentPage(),
	} ) );
	const { viewHelp, viewPrevious } = useDispatch(
		'ithemes-security/dashboard'
	);

	const onClick = () => {
		if ( page === 'help' ) {
			viewPrevious();
		} else {
			viewHelp();
		}
	};

	return (
		<ToolbarButton
			icon="editor-help"
			className="itsec-admin-bar__help"
			onClick={ onClick }
			text={
				page === 'help'
					? __( 'Exit Help', 'it-l10n-ithemes-security-pro' )
					: __( 'Help', 'it-l10n-ithemes-security-pro' )
			}
		/>
	);
}
