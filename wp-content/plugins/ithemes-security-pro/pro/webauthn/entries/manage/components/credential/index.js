/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { dateI18n } from '@wordpress/date';

/**
 * iThemes dependencies
 */
import { Heading, Surface, Text, MessageList, SurfaceVariant, TextSize, TextVariant } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { STORE_NAME } from '../../store';

const StyledCredential = styled( Surface )`
	display: grid;
	grid-template-rows: auto auto auto;
	grid-template-columns: auto min-content;
	grid-template-areas: "error error" "label actions" "details actions";
	align-content: start;
	grid-gap: 0 0.5rem;
	border: 1px ${ ( { isActive } ) => isActive ? 'solid' : 'dashed' } ${ ( { theme } ) => theme.colors.border.normal };
	padding: 0.75rem 1.25rem;
	margin: 0;
`;

const StyledHeading = styled( Heading )`
	grid-area: label;
	font-style: ${ ( { emptyLabel } ) => emptyLabel && 'oblique' };
`;

const StyledDetails = styled.div`
	grid-area: details;
`;

const StyledLastUsedText = styled( Text )`
	font-style: ${ ( { unused } ) => unused && 'oblique' };
`;

const StyledActions = styled( Button )`
	grid-area: actions;
	align-self: center;
`;

const StyledMessageList = styled( MessageList )`
	grid-area: error;
	margin-bottom: 0.75rem;
`;

export default function Credential( { credential, showActions, as, className } ) {
	const { error, isDeleting, isPersisting } = useSelect( ( select ) => ( {
		error: select( STORE_NAME ).getCredentialError( credential.id ),
		isDeleting: select( STORE_NAME ).isDeleting( credential.id ),
		isPersisting: select( STORE_NAME ).isPersisting( credential.id ),
	} ), [ credential.id ] );
	const { deleteCredential, restoreCredential } = useDispatch( STORE_NAME );
	const onClick = () => credential.status === 'active' ? deleteCredential( credential.id ) : restoreCredential( credential.id );

	const isActive = credential.status === 'active';

	return (
		<StyledCredential isActive={ isActive } variant={ SurfaceVariant.PRIMARY } as={ as } className={ className }>
			<StyledMessageList type="danger" messages={ error ? [ error.message || __( 'An unknown error occurred.', 'it-l10n-ithemes-security-pro' ) ] : [] } />

			<StyledHeading
				level={ 3 }
				size={ TextSize.NORMAL }
				weight={ 500 }
				variant={ ( credential.label && isActive ) ? TextVariant.DARK : TextVariant.NORMAL }
				text={ credential.label || __( '(No Label)', 'it-l10n-ithemes-security-pro' ) }
				emptyLabel={ ! credential.label }
			/>
			<StyledDetails>
				<Text variant={ ! isActive && TextVariant.MUTED } as="p" text={
					sprintf(
						/* translators: 1. Date */
						__( 'Added on %s', 'it-l10n-ithemes-security-pro' ),
						dateI18n( 'M j, Y', credential.created_at )
					) }
				/>
				{ isActive && (
					<StyledLastUsedText variant={ ! isActive && TextVariant.MUTED } as="p" unused={ ! credential.last_used } text={
						credential.last_used
							? sprintf(
								/* translators: 1. Date */
								__( 'Last used on %s', 'it-l10n-ithemes-security-pro' ),
								dateI18n( 'M j, Y', credential.last_used )
							) : __( 'Has not been used', 'it-l10n-ithemes-security-pro' )
					}
					/>
				) }
				{ credential.status === 'trash' && (
					<Text variant={ TextVariant.MUTED } as="p" text={
						sprintf(
							/* translators: 1. Date */
							__( 'Deleted on %s', 'it-l10n-ithemes-security-pro' ),
							dateI18n( 'M j, Y', credential.trashed_at )
						) }
					/>
				) }
			</StyledDetails>
			{ showActions && (
				<StyledActions
					isDestructive={ isActive }
					variant="secondary"
					onClick={ onClick }
					disabled={ isDeleting || isPersisting }
					isBusy={ isDeleting || isPersisting }
					key={ credential.status }
					text={ isActive ? __( 'Delete', 'it-l10n-ithemes-security-pro' ) : __( 'Restore', 'it-l10n-ithemes-security-pro' ) }
				/>
			) }
		</StyledCredential>
	);
}
