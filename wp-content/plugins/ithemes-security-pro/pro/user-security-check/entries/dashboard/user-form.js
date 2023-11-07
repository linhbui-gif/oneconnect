/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { compose } from '@wordpress/compose';
import { useState } from '@wordpress/element';
import { withDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { AsyncSelect } from '@ithemes/security-components';

const loadUsers = ( search ) =>
	new Promise( ( resolve, reject ) => {
		apiFetch( {
			path: addQueryArgs( '/wp/v2/users', {
				search,
				per_page: 100,
				itsec_global: true,
			} ),
		} )
			.then( ( response ) =>
				resolve(
					response.map( ( user ) => ( {
						value: user.id,
						label: user.name,
					} ) )
				)
			)
			.catch( reject );
	} );

function UserForm( { card, save } ) {
	const [ userInput, setUserInput ] = useState( 0 );

	return (
		<section className="itsec-card-security-profile__select-user">
			<label
				htmlFor={ `itsec-card-security-profile__select-user-dropdown--${ card.id }` }
			>
				{ __( 'Select a User', 'it-l10n-ithemes-security-pro' ) }
			</label>
			<form className="itsec-card-security-profile__select-form">
				<AsyncSelect
					addErrorBoundary={ false }
					className="itsec-card-security-profile__select-user-dropdown"
					classNamePrefix="itsec-card-security-profile__select-user-dropdown"
					inputId={ `itsec-card-security-profile__select-user-dropdown--${ card.id }` }
					cacheOptions
					defaultOptions
					loadOptions={ loadUsers }
					value={ userInput }
					onChange={ ( option ) => setUserInput( option ) }
					maxMenuHeight={ 150 }
				/>
				<div className="itsec-card-security-profile__select-user-save-container">
					<Button
						onClick={ () =>
							save( {
								...card,
								settings: {
									...( card.settings || {} ),
									user: userInput.value,
								},
							} )
						}
					>
						{ __( 'Select', 'it-l10n-ithemes-security-pro' ) }
					</Button>
				</div>
			</form>
			<p className="description">
				{ __( 'Select a user to monitor with this card.', 'it-l10n-ithemes-security-pro' ) }
			</p>
		</section>
	);
}

export default compose( [
	withDispatch( ( dispatch, ownProps ) => ( {
		unPin() {
			return dispatch( 'ithemes-security/dashboard' ).removeDashboardCard(
				ownProps.dashboardId,
				ownProps.card
			);
		},
		save( card ) {
			return dispatch( 'ithemes-security/dashboard' ).saveDashboardCard(
				ownProps.dashboardId,
				card
			);
		},
	} ) ),
] )( UserForm );
