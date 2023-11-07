/**
 * External dependencies
 */
import {
	useRouteMatch,
	Route,
	Switch,
	useParams,
	useLocation,
	Redirect,
	Link,
} from 'react-router-dom';
import {
	ArrayParam,
	BooleanParam,
	useQueryParam,
	withDefault,
} from 'use-query-params';

/**
 * WordPress dependencies
 */
import { useSelect, useDispatch, useRegistry } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { Flex, FlexItem, Button } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import {
	NavigationFill,
	ChildPages,
	useNavigation,
	useNavigateTo,
	PageHeader,
	HelpFill,
	SelectableCard,
	Breadcrumbs,
} from '@ithemes/security.pages.settings';
import { MODULES_STORE_NAME } from '@ithemes/security.packages.data';
import {
	FlexSpacer,
	HelpList,
	MessageList,
} from '@ithemes/security-components';
import { withNavigate } from '@ithemes/security-hocs';
import { ManageGroup, ManageMultipleGroups } from '../';
import './style.scss';

export default function GroupNav() {
	const { url, path, isExact } = useRouteMatch();
	const { hash, search } = useLocation();
	const { root } = useParams();
	const navigateTo = useNavigateTo();

	const navIds = useSelect( ( select ) =>
		select( 'ithemes-security/user-groups-editor' ).getMatchableNavIds()
	);
	const { createLocalGroup } = useDispatch(
		'ithemes-security/user-groups-editor'
	);

	if ( navIds === null ) {
		return null;
	}

	const onAdd = async () => {
		const { id } = await createLocalGroup();
		navigateTo( `${ url }/${ id }/edit` );
	};

	return (
		<>
			{ ! isExact && (
				<MatchablePages
					navIds={ navIds }
					url={ url }
					search={ search }
					hash={ hash }
				/>
			) }

			<Switch>
				<Route path={ `${ path }/multi` }>
					<MultiRoute navIds={ navIds } />
				</Route>

				<Route path={ `${ path }/:child` }>
					<NavigationFill>
						<Button
							variant="link"
							onClick={ onAdd }
							className="itsec-add-new-user-group-link"
						>
							{ __( 'New Group', 'it-l10n-ithemes-security-pro' ) }
						</Button>
					</NavigationFill>
					<GroupNavRoute root={ root } />
				</Route>

				<Route path={ path }>
					{ navIds.length > 0 &&
						( root === 'onboard' ? (
							<UserGroupsIntro />
						) : (
							<Redirect
								to={ `${ url }/${ navIds[ 0 ] }/${ search }${ hash }` }
							/>
						) ) }
				</Route>
			</Switch>
		</>
	);
}

function UserGroupsIntro() {
	const { root } = useParams();
	const { help } = useSelect(
		( select ) =>
			select( MODULES_STORE_NAME ).getModule( 'user-groups' ) || {}
	);
	const navigateTo = useNavigateTo();
	const registry = useRegistry();
	const { applyDefaultGroupSettings, createDefaultGroups } = useDispatch(
		'ithemes-security/user-groups-editor'
	);
	const doNavigate = () => {
		const navIds = registry
			.select( 'ithemes-security/user-groups-editor' )
			.getMatchableNavIds();
		navigateTo( `/${ root }/user-groups/${ navIds[ 0 ] }` );
	};
	const onCustom = async () => {
		await applyDefaultGroupSettings();
		doNavigate();
	};
	const onDefault = async () => {
		await createDefaultGroups();
		await applyDefaultGroupSettings();
		doNavigate();
	};

	return (
		<>
			<Help />
			<PageHeader
				title={ __( 'User Groups', 'it-l10n-ithemes-security-pro' ) }
				subtitle={ __(
					'User Groups allow you to enable security features for specific sets of users.',
					'it-l10n-ithemes-security-pro'
				) }
				description={
					__(
						'Default groups automatically categorize your users by their WordPress capabilities and enable the recommended security settings for each group. Alternatively, start from scratch with custom groups and categorize your users any way you like.',
						'it-l10n-ithemes-security-pro'
					) +
					' ' +
					__(
						'User Groups won’t change a user’s capabilities, only iThemes Security features are impacted.',
						'it-l10n-ithemes-security-pro'
					)
				}
				help={ help }
			/>
			<MessageList
				hasBorder
				recommended
				messages={ __(
					'Recommended: Default User Groups are the simplest way to get started with iThemes Security.',
					'it-l10n-ithemes-security-pro'
				) }
			/>
			<div className="itsec-user-groups-intro">
				<SelectableCard
					onClick={ onDefault }
					icon="groups"
					title={ __( 'Default', 'it-l10n-ithemes-security-pro' ) }
					description={ __( 'Continue with default groups', 'it-l10n-ithemes-security-pro' ) }
					direction="vertical"
					recommended
				/>
				<SelectableCard
					onClick={ onCustom }
					icon="edit"
					title={ __( 'Custom', 'it-l10n-ithemes-security-pro' ) }
					description={ __( 'Create custom groups', 'it-l10n-ithemes-security-pro' ) }
					direction="vertical"
				/>
			</div>
		</>
	);
}

function MatchablePages( { navIds, url, search, hash } ) {
	const labels = useSelect(
		( select ) =>
			navIds.map(
				select( 'ithemes-security/user-groups-editor' )
					.getEditedMatchableLabel
			),
		[ navIds ]
	);
	const pages = navIds.map( ( navId, i ) => ( {
		title: labels[ i ] || __( 'Untitled', 'it-l10n-ithemes-security-pro' ),
		to: `${ url }/${ navId }${ search }${ hash }`,
		id: navId,
	} ) );

	return <ChildPages pages={ pages } />;
}

function GroupNavRoute( { root } ) {
	const { child: groupId } = useParams();
	const { previous, next, nextPage } = useNavigation();

	return (
		<>
			<Help />
			<ManageGroup groupId={ groupId } showSave={ root === 'settings' } />

			{ root !== 'settings' && (
				<Flex>
					{ previous && (
						<FlexItem>
							<Link
								component={ withNavigate( Button ) }
								variant="tertiary"
								type="button"
								to={ previous }
							>
								{ __( 'Back', 'it-l10n-ithemes-security-pro' ) }
							</Link>
						</FlexItem>
					) }
					<FlexSpacer />
					{ next && (
						<>
							<FlexItem>
								<Link
									component={ withNavigate( Button ) }
									variant="primary"
									to={ next }
								>
									{ __( 'Next', 'it-l10n-ithemes-security-pro' ) }
								</Link>
							</FlexItem>
							<FlexItem>
								<Link
									component={ withNavigate( Button ) }
									variant="primary"
									to={ nextPage }
									text={ __( 'Skip User Groups', 'it-l10n-ithemes-security-pro' ) }
									icon="arrow-right-alt"
									iconPosition="right"
									className="itsec-button-icon-right"
								/>
							</FlexItem>
						</>
					) }
				</Flex>
			) }
		</>
	);
}

function MultiRoute( { navIds } ) {
	const [ selected, setSelected ] = useQueryParam(
		'id',
		withDefault( ArrayParam, [] )
	);
	const [ isAll, setIsAll ] = useQueryParam( 'all', BooleanParam );

	useEffect( () => {
		if ( isAll ) {
			setSelected( navIds );
			setIsAll( undefined );
		}
	}, [ setSelected, isAll, setIsAll, navIds ] );

	return <ManageMultipleGroups groupIds={ selected } />;
}

function Help() {
	const match = useRouteMatch();
	const { help } = useSelect(
		( select ) =>
			select( MODULES_STORE_NAME ).getModule( 'user-groups' ) || {}
	);

	return (
		<HelpFill>
			<PageHeader
				title={ __( 'User Groups', 'it-l10n-ithemes-security-pro' ) }
				description={ help }
				breadcrumbs={
					<Breadcrumbs
						title={ __( 'Help', 'it-l10n-ithemes-security-pro' ) }
						match={ match }
					/>
				}
			/>
			<HelpList topic="user-groups" />
		</HelpFill>
	);
}
