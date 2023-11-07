/**
 * External dependencies
 */
import { noop, once } from 'lodash';

/**
 * WordPress dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { TabPanel } from '@ithemes/security-components';
import UserTab from './user-tab';
import RoleTab from './role-tab';
import './style.scss';

const getTabs = once( () => [
	{
		name: 'user',
		title: __( 'Users', 'it-l10n-ithemes-security-pro' ),
		Component: UserTab,
		type: 'button',
		count( share ) {
			return sprintf(
				/* translators: 1. The number of users. */
				_n( '%d user', '%d users', share.users.length, 'it-l10n-ithemes-security-pro' ),
				share.users.length
			);
		},
	},
	{
		name: 'role',
		title: __( 'Roles', 'it-l10n-ithemes-security-pro' ),
		type: 'button',
		Component: RoleTab,
		count( share ) {
			return sprintf(
				/* translators: 1. The number of roles. */
				_n( '%d role', '%d roles', share.roles.length, 'it-l10n-ithemes-security-pro' ),
				share.roles.length
			);
		},
	},
] );

export default function ShareAdd( {
	dashboardId,
	close,
} ) {
	const [ shares, setShares ] = useState( {} );

	const { isSaving, dashboard } = useSelect( ( select ) => ( {
		isSaving: select( 'ithemes-security/dashboard' ).isSavingDashboard( dashboardId ),
		dashboard: select( 'ithemes-security/dashboard' ).getDashboardForEdit( dashboardId ),
	} ), [ dashboardId ] );

	const { saveDashboard: save } = useDispatch( 'ithemes-security/dashboard' );

	const onSubmit = async ( e ) => {
		e.preventDefault();

		if ( ! isSaving ) {
			await save( {
				...dashboard,
				sharing: [
					...dashboard.sharing,
					...Object.values( shares ).filter( ( share ) => share ),
				],
			} );
			setShares( {} );
			close();
		}
	};

	const tabs = getTabs();
	const summary = tabs.reduce( ( acc, cur ) => {
		if ( shares[ cur.name ] ) {
			acc.push( cur.count( shares[ cur.name ] ) );
		}

		return acc;
	}, [] );

	return (
		<form
			className="itsec-share-dashboard-add"
			onSubmit={ ( e ) => e.preventDefault() }
		>
			<header className="itsec-share-dashboard-add__header">
				<h3>{ __( 'Share Dashboard', 'it-l10n-ithemes-security-pro' ) }</h3>
				<p>
					{ __(
						'Give select users read-only access to this dashboard. Great for building client portals.',
						'it-l10n-ithemes-security-pro'
					) }
				</p>
			</header>
			<TabPanel
				className="itsec-share-dashboard-add__tab-panel"
				tabs={ tabs }
				isStyled
			>
				{ ( { name, Component = noop } ) => (
					<Component
						dashboardId={ dashboardId }
						share={ shares[ name ] }
						onChange={ ( share ) =>
							setShares( { ...shares, [ name ]: share } )
						}
					/>
				) }
			</TabPanel>
			<footer className="itsec-share-dashboard-add__footer">
				{ summary.length > 0 && (
					<span className="itsec-share-dashboard-add__summary">
						{ sprintf(
							/* translators: 1. List of names. */
							__( '%s selected', 'it-l10n-ithemes-security-pro' ),
							summary.join( ', ' )
						) }
					</span>
				) }
				<Button
					variant="primary"
					type="submit"
					onClick={ onSubmit }
					isBusy={ isSaving }
					aria-disabled={ isSaving }
				>
					{ __( 'Share', 'it-l10n-ithemes-security-pro' ) }
				</Button>
			</footer>
		</form>
	);
}
