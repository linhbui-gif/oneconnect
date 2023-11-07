/**
 * External dependencies
 */
import { get, map, without } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { Dashicon, Button } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import {
	compose,
	withInstanceId,
	useInstanceId,
	useDebounce,
} from '@wordpress/compose';
import {
	useDispatch,
	useSelect,
	withDispatch,
	withSelect,
} from '@wordpress/data';
import { useCallback, useEffect, useState } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';

/**
 * iThemes dependencies
 */
import { SearchControl } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import {
	CardHeader,
	CardHeaderTitle,
	CardHeaderStatus,
	MasterDetail,
	MasterDetailBack,
} from '@ithemes/security.dashboard.dashboard';
import { withProps } from '@ithemes/security-hocs';
import { ChipControl, SplitButton } from '@ithemes/security-components';
import UserInfo from './user-info';
import { getTwoFactor, useCardLink } from './utils';

function MasterRender( { master } ) {
	return (
		<>
			<td className="itsec-card-security-profile__users--column-avatar">
				<img src={ master.avatar } alt="" />
			</td>
			<th
				scope="row"
				className="itsec-card-security-profile__users--column-username"
			>
				{ master.name }
			</th>
			<td className="itsec-card-security-profile__users--column-role">
				{ master.role }
			</td>
			<td className="itsec-card-security-profile__users--column-two-factor">
				<Dashicon icon={ getTwoFactor( master.two_factor )[ 0 ] } />
				<span className="screen-reader-text">
					{ getTwoFactor( master.two_factor )[ 1 ] }
				</span>
			</td>
		</>
	);
}

function DetailRender( { master, pinUser, profileCards, card } ) {
	return (
		<section className="itsec-card-security-profile__user">
			<header className="itsec-card-security-profile__user-header">
				<img src={ master.avatar } alt="" />
				<h3>{ master.name }</h3>
				{ ! profileCards.includes( master.id ) && (
					<Button variant="link" onClick={ () => pinUser( master.id ) }>
						{ __( 'Pin', 'it-l10n-ithemes-security-pro' ) }
					</Button>
				) }
			</header>
			<UserInfo user={ master } card={ card } />
		</section>
	);
}

function SecurityProfile( {
	card,
	config,
	eqProps,
	profileCards,
	pinUser,
} ) {
	const [ selected, setSelected ] = useState( 0 );
	const detailRender = withProps( { pinUser, profileCards, card } )(
		DetailRender
	);

	const select = ( id ) => setSelected( id );
	const isSmall =
		eqProps[ 'max-width' ] && eqProps[ 'max-width' ].includes( '500px' );

	return (
		<div className="itsec-card--type-security-profile-list">
			<CardHeader>
				<MasterDetailBack
					isSmall={ isSmall }
					select={ select }
					selectedId={ selected }
				/>
				<CardHeaderTitle card={ card } config={ config } />
				<CardHeaderStatus />
			</CardHeader>
			<MasterDetail
				masters={ card.data.users }
				detailRender={ detailRender }
				masterRender={ MasterRender }
				ListHeader={ Filter }
				ListFooter={ Footer }
				selectedId={ selected }
				select={ select }
				isSmall={ isSmall }
				context={ card }
			>
				<thead>
					<tr>
						<th className="itsec-card-security-profile__users--column-avatar">
							<span className="screen-reader-text">
								{ __( 'Avatar', 'it-l10n-ithemes-security-pro' ) }
							</span>
						</th>
						<th className="itsec-card-security-profile__users--column-username">
							{ __( 'Username', 'it-l10n-ithemes-security-pro' ) }
						</th>
						<th className="itsec-card-security-profile__users--column-role">
							{ __( 'Role', 'it-l10n-ithemes-security-pro' ) }
						</th>
						<th className="itsec-card-security-profile__users--column-two-factor">
							{ __( '2FA', 'it-l10n-ithemes-security-pro' ) }
						</th>
					</tr>
				</thead>
			</MasterDetail>
		</div>
	);
}

function Filter( { context: card } ) {
	const rolesId = useInstanceId(
		Filter,
		'itsec-card-security-profile__filter-roles'
	);

	const { roles, config, isQuerying } = useSelect(
		( select ) => ( {
			roles: select( 'ithemes-security/core' ).getRoles(),
			config: select( 'ithemes-security/dashboard' ).getAvailableCard(
				card.card
			),
			isQuerying: select(
				'ithemes-security/dashboard'
			).isQueryingDashboardCard( card.id ),
		} ),
		[ card.id, card.card ]
	);
	const { queryDashboardCard } = useDispatch( 'ithemes-security/dashboard' );
	const query = useCallback(
		( searchParam, rolesParam ) => {
			queryDashboardCard( card.id, {
				search: searchParam,
				roles: rolesParam,
			} );
		},
		[ card.id ]
	);
	const debounced = useDebounce( query, 300 );

	const [ search, setSearch ] = useState( '' );
	const [ showRoles, setShowRoles ] = useState( false );
	const [ selectedRoles, selectRoles ] = useState(
		config.query_args.roles?.default || []
	);

	useEffect( () => debounced( search, selectedRoles ), [
		search,
		selectedRoles,
	] );

	return (
		<div
			className={ classnames( 'itsec-card-security-profile__filter', {
				'itsec-card-security-profile__filter--roles-visible': showRoles,
				'itsec-card-security-profile__filter--has-roles':
					config.query_args.roles,
			} ) }
		>
			<div className="itsec-card-security-profile__filter-search">
				<SearchControl
					value={ search }
					onChange={ setSearch }
					label={ __( 'Search Users', 'it-l10n-ithemes-security-pro' ) }
					placeholder={ __( 'Search Users' ) }
					isSearching={ isQuerying }
					surfaceVariant="secondary"
				/>
			</div>
			{ config.query_args.roles && (
				<Button
					icon="filter"
					onClick={ () => setShowRoles( ! showRoles ) }
					aria-expanded={ showRoles }
					aria-controls={ rolesId }
				>
					{ sprintf(
						/* translators: 1. Number of roles. */
						__( 'Roles (%d)', 'it-l10n-ithemes-security-pro' ),
						selectedRoles.length
					) }
				</Button>
			) }
			{ showRoles && (
				<ul id={ rolesId }>
					{ map( roles, ( role, slug ) => (
						<li key={ slug }>
							<ChipControl
								label={ role.label }
								checked={ selectedRoles.includes( slug ) }
								onChange={ ( checked ) =>
									selectRoles(
										checked
											? [ ...selectedRoles, slug ]
											: without( selectedRoles, slug )
									)
								}
							/>
						</li>
					) ) }
				</ul>
			) }
		</div>
	);
}

function Footer( { context: card } ) {
	const { refreshDashboardCard } = useDispatch(
		'ithemes-security/dashboard'
	);

	const forceLink =
		card._links?.[ 'ithemes-security:force-password-change' ]?.[ 0 ];
	const clearLink =
		card._links?.[ 'ithemes-security:clear-password-change' ]?.[ 0 ];

	const { execute: forceExecute, status: forceStatus } = useCardLink(
		forceLink,
		() => refreshDashboardCard( card.id ),
		false
	);

	const { execute: clearExecute, status: clearStatus } = useCardLink(
		clearLink,
		() => refreshDashboardCard( card.id ),
		false
	);

	if ( ! forceLink ) {
		return null;
	}

	return (
		<footer className="itsec-card-security-profile__footer">
			<SplitButton
				isSmall
				variant="primary"
				isBusy={
					forceStatus === 'pending' || clearStatus === 'pending'
				}
				text={ forceLink.title }
				onClick={ () => forceExecute() }
				controls={ [
					clearLink && {
						title: clearLink.title,
						onClick() {
							clearExecute();
						},
					},
				] }
			/>
		</footer>
	);
}

export const slug = 'security-profile-list';
export const settings = {
	render: compose( [
		withInstanceId,
		withDispatch( ( dispatch, ownProps ) => ( {
			pinUser( uid ) {
				dispatch( 'ithemes-security/dashboard' ).saveDashboardCard(
					ownProps.dashboardId,
					{
						card: 'security-profile',
						settings: {
							user: uid,
						},
					}
				);
			},
		} ) ),
		withSelect( ( select, ownProps ) => ( {
			profileCards: select( 'ithemes-security/dashboard' )
				.getDashboardCards( ownProps.dashboardId )
				.filter( ( card ) => card.card === 'security-profile' )
				.map( ( card ) => get( card, [ 'data', 'user', 'id' ] ) ),
		} ) ),
	] )( SecurityProfile ),
	elementQueries: [
		{
			type: 'width',
			dir: 'max',
			px: 500,
		},
		{
			type: 'width',
			dir: 'min',
			px: 501,
		},
		{
			type: 'width',
			dir: 'max',
			px: 700,
		},
	],
};

addFilter(
	'ithemes-security.dashboard.getCardTitle.security-profile',
	'ithemes-security/security-profile/default',
	function( title, card ) {
		if ( card.data.user && card.data.user.name ) {
			return sprintf(
				/* translators: 1. The user's name. */
				__( 'Security Profile – %s', 'it-l10n-ithemes-security-pro' ),
				card.data.user.name
			);
		}

		if ( card.settings && card.settings.user ) {
			return sprintf(
				/* translators: 1. The user ID. */
				__( 'User (%d) Security Profile', 'it-l10n-ithemes-security-pro' ),
				card.settings.user
			);
		}

		return title;
	}
);
