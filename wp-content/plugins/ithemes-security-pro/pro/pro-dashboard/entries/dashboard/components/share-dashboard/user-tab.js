/**
 * External dependencies
 */
import memize from 'memize';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';
import { Fragment, useState } from '@wordpress/element';
import { withSelect, withDispatch } from '@wordpress/data';
import { CheckboxControl, Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { AsyncSelect } from '@ithemes/security-components';

const loadUsers = memize( ( exclude = [] ) => ( search ) =>
	new Promise( ( resolve, reject ) => {
		apiFetch( {
			path: addQueryArgs( '/wp/v2/users', {
				search,
				per_page: 100,
				exclude,
			} ),
		} )
			.then( ( response ) =>
				resolve(
					response.map( ( user ) => ( {
						value: user.id,
						label: user.name,
						user,
					} ) )
				)
			)
			.catch( reject );
	} )
);

const AddedUser = compose( [
	withSelect( ( select, props ) => ( {
		user: select( 'ithemes-security/dashboard' ).getUser( props.userId ),
	} ) ),
] )( ( { user } ) => <li>{ user.name }</li> );

const excludedUsers = memize( ( sharing = [] ) =>
	[].concat( ...sharing.map( ( share ) => share.users || [] ) )
);

function UserTab( {
	suggested,
	dashboard,
	share = { type: 'user', users: [] },
	onChange,
	receiveUser,
} ) {
	const [ selectedUser, setSelectedUser ] = useState( undefined );
	const [ selectSearch, setSelectSearch ] = useState( '' );

	const addUser = ( e ) => {
		e.preventDefault();

		if ( ! selectedUser ) {
			return;
		}

		receiveUser( selectedUser.user );
		onChange( { ...share, users: [ ...share.users, selectedUser.value ] } );
		setSelectedUser( false );
		setSelectSearch( '' );
	};

	const exclude = excludedUsers( dashboard.sharing ).concat(
		dashboard.created_by
	);
	const suggestedFiltered = suggested.filter(
		( user ) => ! exclude.includes( user.id )
	);

	return (
		<Fragment>
			{ suggestedFiltered.length > 0 && (
				<fieldset className="itsec-share-dashboard__suggested-users">
					<legend>{ __( 'Suggested Users', 'it-l10n-ithemes-security-pro' ) }</legend>
					<ul>
						{ suggestedFiltered.map( ( user ) => (
							<li key={ user.id }>
								<CheckboxControl
									label={ user.name }
									checked={ share.users.includes( user.id ) }
									onChange={ ( checked ) =>
										checked
											? onChange( {
												...share,
												users: [
													...share.users,
													user.id,
												],
											} )
											: onChange( {
												...share,
												users: share.users.filter(
													( userId ) =>
														userId !== user.id
												),
											} )
									}
								/>
							</li>
						) ) }
					</ul>
				</fieldset>
			) }
			<fieldset className="itsec-share-dashboard__add-users">
				<legend>{ __( 'All Users', 'it-l10n-ithemes-security-pro' ) }</legend>
				<ul>
					{ share.users
						.filter(
							( userId ) =>
								! suggested.some(
									( suggestion ) => suggestion.id === userId
								)
						)
						.map( ( userId ) => (
							<AddedUser key={ userId } userId={ userId } />
						) ) }
				</ul>

				<label
					className="itsec-share-dashboard__add-users-select"
					htmlFor="itsec-share-dashboard__add-users-select"
				>
					{ __( 'Select a User', 'it-l10n-ithemes-security-pro' ) }
				</label>

				<div className="itsec-share-dashboard__add-users-fields">
					<AsyncSelect
						className="itsec-share-dashboard__add-users-select-dropdown"
						inputId="itsec-share-dashboard__add-users-select"
						cacheOptions
						defaultOptions
						loadOptions={ loadUsers( exclude ) }
						value={ selectedUser }
						onChange={ ( option ) =>
							setSelectedUser( option )
						}
						inputValue={ selectSearch }
						onInputChange={ ( newSelect ) =>
							setSelectSearch( newSelect )
						}
						maxMenuHeight={ 150 }
						menuPlacement="top"
					/>

					<div className="itsec-share-dashboard__add-users-trigger">
						<Button onClick={ addUser } disabled={ ! selectedUser }>
							{ __( 'Select', 'it-l10n-ithemes-security-pro' ) }
						</Button>
					</div>
				</div>
			</fieldset>
		</Fragment>
	);
}

export default compose( [
	withSelect( ( select, props ) => ( {
		suggested: select(
			'ithemes-security/dashboard'
		).getSuggestedShareUsers(),
		dashboard: select( 'ithemes-security/dashboard' ).getDashboardForEdit(
			props.dashboardId
		),
	} ) ),
	withDispatch( ( dispatch ) => ( {
		receiveUser: dispatch( 'ithemes-security/dashboard' ).receiveUser,
	} ) ),
] )( UserTab );
