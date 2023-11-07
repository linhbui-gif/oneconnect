/**
 * WordPress dependencies
 */
import { Fragment } from '@wordpress/element';
import { withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import {
	CardHeader,
	CardHeaderTitle,
} from '@ithemes/security.dashboard.dashboard';
import UserInfo from './user-info';
import UserForm from './user-form';
import './style.scss';

function Pinned( { card, config, canEdit, dashboardId } ) {
	const selectedUser = card.data.user;

	return (
		<div className="itsec-card--type-security-profile">
			{ selectedUser && (
				<section className="itsec-card-security-profile__user">
					<header className="itsec-card-security-profile__user-header itsec-card__drag-handle">
						<img src={ selectedUser.avatar } alt="" />
						<h3>{ selectedUser.name }</h3>
					</header>
					<UserInfo user={ selectedUser } card={ card } />
				</section>
			) }
			{ ! selectedUser && (
				<Fragment>
					<CardHeader>
						<CardHeaderTitle card={ card } config={ config } />
					</CardHeader>
					{ canEdit && (
						<UserForm card={ card } dashboardId={ dashboardId } />
					) }
				</Fragment>
			) }
		</div>
	);
}

export const slug = 'security-profile';

export const settings = {
	render: compose( [
		withSelect( ( select, ownProps ) => ( {
			canEdit: select( 'ithemes-security/dashboard' ).canEditCard(
				ownProps.dashboardId,
				ownProps.card.id
			),
		} ) ),
	] )( Pinned ),
	elementQueries: [
		{
			type: 'width',
			dir: 'max',
			px: 250,
		},
	],
};
