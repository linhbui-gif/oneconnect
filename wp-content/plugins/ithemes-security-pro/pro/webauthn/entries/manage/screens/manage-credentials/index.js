/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button, Card, CardBody, CardFooter } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * iThemes dependencies
 */
import { DiscreteProgressHeader, Heading, MessageList, SurfaceVariant, TextSize, TextWeight } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { Credential } from '../../components';
import { STORE_NAME } from '../../store';

export default function ManageCredentials( { onAddNew, onComplete } ) {
	const { credentials, error } = useSelect( ( select ) => ( {
		credentials: select( STORE_NAME ).getCredentials(),
		error: select( STORE_NAME ).getFetchCredentialsError(),
	} ), [] );

	return (
		<Card>
			<DiscreteProgressHeader surfaceVariant={ SurfaceVariant.PRIMARY_ACCENT } justifyChildren="center">
				<Heading
					level={ 2 }
					align="center"
					size={ TextSize.EXTRA_LARGE }
					weight={ TextWeight.HEAVY }
					text={ __( 'Manage Passkeys', 'it-l10n-ithemes-security-pro' ) }
				/>
			</DiscreteProgressHeader>
			<CardBody>
				<MessageList type="danger" messages={ error ? [ error.message || __( 'Could not fetch passkeys.', 'it-l10n-ithemes-security-pro' ) ] : [] } />
				<CredentialList credentials={ credentials } />
			</CardBody>
			<CardFooter>
				<Button onClick={ onComplete } variant="link" text={ __( 'Done', 'it-l10n-ithemes-security-pro' ) } />
				<Button onClick={ onAddNew } variant="primary" text={ __( 'Add a Passkey', 'it-l10n-ithemes-security-pro' ) } />
			</CardFooter>
		</Card>
	);
}

const StyledList = styled.ul`
	list-style: none;
	margin: 0;
	padding: 0;
`;

const StyledCredential = styled( Credential )`
	&:not(:last-child) {
		margin-bottom: 0.75rem;
	}
`;

function CredentialList( { credentials } ) {
	return (
		<StyledList>
			{ credentials.map( ( credential ) => (
				<StyledCredential key={ credential.id } credential={ credential } as="li" showActions />
			) ) }
		</StyledList>
	);
}

