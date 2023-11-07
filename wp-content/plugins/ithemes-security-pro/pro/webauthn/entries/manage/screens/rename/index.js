/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button, Card, CardBody, TextControl } from '@wordpress/components';
import { createInterpolateElement, useState } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { check } from '@wordpress/icons';

/**
 * iThemes dependencies
 */
import { DiscreteProgressHeader, Text, MessageList, SurfaceVariant, TextSize, TextWeight } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { STORE_NAME } from '../../store';

const StyledBody = styled( CardBody )`
	display: flex;
	flex-direction: column;
	gap: 1.75rem;
	padding: 2rem 3rem;
`;

const StyledTextControl = styled( TextControl )`
	input[type="text"] {
		margin: 0;
	}
`;

const StyledSettingsText = styled( Text )`
	max-width: 12rem;
	align-self: center;
`;

const StyledDoneButton = styled( Button )`
	align-self: center;
`;

export default function Rename( { onContinue } ) {
	const { isPersisting, error } = useSelect( ( select ) => ( {
		error: select( STORE_NAME ).getCredentialError( select( STORE_NAME ).getNewCredentialId() ),
		isPersisting: select( STORE_NAME ).isPersisting( select( STORE_NAME ).getNewCredentialId() ),
	} ), [] );
	const { persistNewCredentialLabel } = useDispatch( STORE_NAME );

	const [ label, setLabel ] = useState( '' );

	const rename = () => label.length > 0 && persistNewCredentialLabel( label ).then( onContinue );

	return (
		<Card>
			<DiscreteProgressHeader
				surfaceVariant={ SurfaceVariant.PRIMARY_ACCENT }
				currentStep={ 3 }
				justifyChildren="center"
			>
				<Text icon={ check } iconSize={ 120 } align="center" />
				<Text
					align="center"
					size={ TextSize.EXTRA_LARGE }
					weight={ TextWeight.HEAVY }
					text={ __( 'Success!', 'it-l10n-ithemes-security-pro' ) }
				/>
			</DiscreteProgressHeader>
			<StyledBody>
				<MessageList type="danger" messages={ error ? [ error.message || __( 'An unexpected error occurred.', 'it-l10n-ithemes-security-pro' ) ] : [] } />
				<Text
					as="p"
					text={ __( 'The last step is to name your device…', 'it-l10n-ithemes-security-pro' ) }
				/>
				<StyledTextControl
					required
					minLength={ 1 }
					value={ label }
					onChange={ setLabel }
					label={ __( 'Passkey Name', 'it-l10n-ithemes-security-pro' ) }
					description={ __( 'Choose a name that will help you remember the device you registered.', 'it-l10n-ithemes-security-pro' ) }
				/>
				<StyledSettingsText
					as="p"
					align="center"
					text={ createInterpolateElement( __( 'You can manage your passkeys in your <b>Profile settings</b>.', 'it-l10n-ithemes-security-pro' ), {
						b: <strong />,
					} ) }
				/>
				<StyledDoneButton
					variant="primary"
					onClick={ rename }
					disabled={ ! label.length || isPersisting }
					isBusy={ isPersisting }
					text={ __( 'Done', 'it-l10n-ithemes-security-pro' ) }
				/>
			</StyledBody>
		</Card>
	);
}
