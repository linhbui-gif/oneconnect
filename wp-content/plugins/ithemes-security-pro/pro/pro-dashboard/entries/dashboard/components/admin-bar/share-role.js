/**
 * External dependencies
 */
import { get } from 'lodash';
import contrast from 'contrast';
/**
 * WordPress dependencies
 */
import { Button, Dropdown, Tooltip } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
/**
 * Internal dependencies
 */
import { PRIMARYS } from '@ithemes/security-style-guide';

const sumChars = ( str ) => {
	let sum = 0;

	for ( let i = 0; i < str.length; i++ ) {
		sum += str.charCodeAt( i );
	}

	return sum;
};

export default function ShareRole( { dashboardId, role } ) {
	const { dashboard, roles } = useSelect(
		( select ) => ( {
			dashboard: select(
				'ithemes-security/dashboard'
			).getDashboardForEdit( dashboardId ),
			roles: select( 'ithemes-security/core' ).getRoles(),
		} ),
		[ dashboardId ]
	);
	const { saveDashboard } = useDispatch( 'ithemes-security/dashboard' );

	const remove = () => {
		const sharing = [];

		for ( const share of dashboard.sharing ) {
			if ( share.type !== 'role' ) {
				sharing.push( share );
			} else if ( ! share.roles.includes( role ) ) {
				sharing.push( share );
			} else {
				const without = {
					...share,
					roles: share.roles.filter(
						( maybeRole ) => maybeRole !== role
					),
				};

				sharing.push( without );
			}
		}

		return saveDashboard( { ...dashboard, sharing } );
	};

	const label = get( roles, [ role, 'label' ], role );

	const parts = label.split( ' ' );
	let abbr;

	if ( parts.length === 1 ) {
		abbr = label.substring( 0, 2 );
	} else {
		abbr =
			parts[ 0 ].substring( 0, 1 ).toUpperCase() +
			parts[ 1 ].substring( 0, 1 ).toUpperCase();
	}

	const backgroundColor = PRIMARYS[ sumChars( role ) % PRIMARYS.length ];

	return (
		<Dropdown
			className="itsec-admin-bar-share__recipient itsec-admin-bar-share__recipient--role"
			contentClassName="itsec-admin-bar-share__recipient-content itsec-admin-bar-share__recipient-content--role"
			headerTitle={ sprintf(
				/* translators: 1. The dashboard name. */
				__( 'Share Settings for %s', 'it-l10n-ithemes-security-pro' ),
				label
			) }
			focusOnMount="container"
			expandOnMobile
			renderToggle={ ( { isOpen, onToggle } ) => (
				<Tooltip text={ label }>
					<Button
						aria-pressed={ isOpen }
						onClick={ onToggle }
						className="itsec-admin-bar-share__recipient-trigger"
						aria-label={ label }
						style={ { backgroundColor } }
					>
						<span
							className={ `itsec-admin-bar-share__role-abbr itsec-admin-bar-share__role-abbr--theme-${ contrast(
								backgroundColor
							) }` }
						>
							{ abbr }
						</span>
					</Button>
				</Tooltip>
			) }
			renderContent={ () => (
				<Fragment>
					<header>
						<h3>{ label }</h3>
					</header>
					<footer>
						<Button variant="link" onClick={ remove }>
							{ __( 'Remove', 'it-l10n-ithemes-security-pro' ) }
						</Button>
					</footer>
				</Fragment>
			) }
		/>
	);
}
