/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { Button, Card, CardBody, CardFooter, Flex, FlexItem } from '@wordpress/components';
import { useState, createInterpolateElement } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { dateI18n } from '@wordpress/date';

/**
 * iThemes dependencies
 */
import {
	DiscreteProgressHeader,
	Heading,
	Text,
	Surface,
	StepIndicator,
	MessageList,
	SurfaceVariant,
	TextSize,
	TextVariant,
	TextWeight,
} from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { registerCredential } from '@ithemes/security.webauthn.utils';
import { LearnMore } from '../../components';
import { STORE_NAME } from '../../store';

const StyledBody = styled( CardBody )`
	display: flex;
	flex-direction: column;
	gap: 0.75rem;
`;

const StyledStepGrid = styled.div`
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	grid-template-rows: repeat(2, auto);
	grid-auto-flow: column;
	gap: 1rem 0.75rem;
`;

const StyledSkipItem = styled( FlexItem )`
	margin-right: auto;
`;

function sleep( ms ) {
	return new Promise( ( resolve ) => setTimeout( resolve, ms ) );
}

/**
 * Gets the authenticator selection criteria for the given mode.
 *
 * @param {string} mode
 * @return {window.AuthenticatorSelectionCriteria} Selection criteria.
 */
function getAuthenticatorSelection( mode ) {
	if ( mode === 'cross-platform' ) {
		return {
			authenticatorAttachment: 'cross-platform',
		};
	}
	return {
		residentKey: 'preferred',
	};
}

export default function AddCredential( { onContinue, skipText, onSkip } ) {
	const { error, isRegistering, canRegisterPlatform } = useSelect( ( select ) => ( {
		error: select( STORE_NAME ).getRegisterError(),
		isRegistering: select( STORE_NAME ).isRegistering(),
		canRegisterPlatform: select( STORE_NAME ).canRegisterPlatformAuthenticator(),
	} ), [] );
	const { registerCredential: dispatchRegister } = useDispatch( STORE_NAME );

	const [ currentStep, setCurrentStep ] = useState( 1 );

	const register = ( mode ) => {
		const label = sprintf(
			/* translators: Date */
			__( 'Registered on %s.', 'it-l10n-ithemes-security-pro' ),
			dateI18n( 'M j, Y' )
		);
		const authenticatorSelection = getAuthenticatorSelection( mode );
		const registerPromise = registerCredential( { label, authenticatorSelection } );

		// eslint-disable-next-line no-console
		return dispatchRegister( mode, registerPromise ).then( onContinue ).catch( console.error );
	};
	const onAdd = async ( mode ) => {
		if ( currentStep === 1 ) {
			setCurrentStep( 2 );
			await sleep( 100 );
		}

		register( mode );
	};

	return (
		<Card>
			<Surface variant={ SurfaceVariant.PRIMARY }>
				<DiscreteProgressHeader
					surfaceVariant={ SurfaceVariant.PRIMARY_ACCENT }
					currentStep={ currentStep }
					justifyChildren="center"
					alignChildren="center"
				>
					<Heading
						level={ 2 }
						align="center"
						size={ TextSize.EXTRA_LARGE }
						weight={ TextWeight.HEAVY }
						text={ __( 'Setup Passkey Login', 'it-l10n-ithemes-security-pro' ) }
					/>
				</DiscreteProgressHeader>
				<StyledBody>
					{ ! canRegisterPlatform && <MessageList type="warning" messages={ [ __( 'Your device doesn’t support passkeys.', 'it-l10n-ithemes-security-pro' ) ] } /> }
					<MessageList type="danger" messages={ error ? [ error.message || __( 'An unexpected error occurred.', 'it-l10n-ithemes-security-pro' ) ] : [] } />
					{ canRegisterPlatform ? (
						<>
							<Heading
								level={ 2 }
								size={ TextSize.LARGE }
								weight={ TextWeight.HEAVY }
								variant={ TextVariant.ACCENT }
								text={ __( 'Why:', 'it-l10n-ithemes-security-pro' ) }
							/>
							<Text as="p" text={ createInterpolateElement(
								__( 'Passkeys <b>improve security</b> and <b>speed up the login process</b> by using authentication built into your device instead of passwords. This can mean biometrics like Face ID, Touch ID, or Windows Hello. If your device doesn’t have those capabilities, don’t worry, you can still use passkeys.', 'it-l10n-ithemes-security-pro' ),
								{
									b: <strong />,
								}
							) } />
							<Text as="p" text={ __( 'When authenticating with a passkey, your personal information never leaves your device. Hackers can’t leak passkeys or trick you into sharing them.', 'it-l10n-ithemes-security-pro' ) } />
						</>
					) : (
						<>
							<Text as="p" text={ createInterpolateElement(
								__( 'It looks like passkeys, also known as Platform Authenticators, aren’t available for your device yet.', 'it-l10n-ithemes-security-pro' ) + ' ' +
								__( 'Read more about <a>device compatibility</a>.', 'it-l10n-ithemes-security-pro' ),
								{
								// eslint-disable-next-line jsx-a11y/anchor-has-content
									a: <a href="https://webauthn.me/browser-support" target="_blank" rel="noreferrer" />,
								}
							) } />
							<Text as="p" text={ __( 'If you have a YubiKey, Titan Key, or another USB Security Key, you can still register that device.', 'it-l10n-ithemes-security-pro' ) } />
						</>
					) }
					<Heading
						level={ 2 }
						size={ TextSize.LARGE }
						weight={ TextWeight.HEAVY }
						variant={ TextVariant.ACCENT }
						text={ __( 'How it works:', 'it-l10n-ithemes-security-pro' ) }
					/>
					<Steps currentStep={ currentStep } />
					<LearnMore />
				</StyledBody>
				<CardFooter>
					<Flex>
						<StyledSkipItem>
							<Button variant="link" onClick={ onSkip } text={ skipText } />
						</StyledSkipItem>
						<FlexItem>
							<Button
								variant="secondary"
								text={ __( 'Add USB Security Key', 'it-l10n-ithemes-security-pro' ) }
								isBusy={ isRegistering === 'cross-platform' }
								disabled={ isRegistering }
								onClick={ () => onAdd( 'cross-platform' ) }
							/>
						</FlexItem>
						<FlexItem>
							<Button
								variant="primary"
								onClick={ () => onAdd( 'platform' ) }
								isBusy={ isRegistering === 'platform' }
								disabled={ isRegistering || ! canRegisterPlatform }
								text={ __( 'Add Passkey', 'it-l10n-ithemes-security-pro' ) }
							/>
						</FlexItem>
					</Flex>
				</CardFooter>
			</Surface>
		</Card>
	);
}

function Steps( { currentStep } ) {
	return (
		<StyledStepGrid>
			<StepIndicator step={ 1 } surfaceVariant={ SurfaceVariant.PRIMARY_ACCENT } invert={ currentStep !== 1 } />
			<Text as="p" text={ __( 'Press “Add Passkey” to register your device.', 'it-l10n-ithemes-security-pro' ) } />
			<StepIndicator step={ 2 } surfaceVariant={ SurfaceVariant.PRIMARY_ACCENT } invert={ currentStep !== 2 } />
			<Text as="p" text={ __( 'A pop up will appear, follow it’s instructions.', 'it-l10n-ithemes-security-pro' ) } />
			<StepIndicator step={ 3 } surfaceVariant={ SurfaceVariant.PRIMARY_ACCENT } invert={ currentStep !== 3 } />
			<Text as="p" text={ __( 'Name your passkey and you’re done!', 'it-l10n-ithemes-security-pro' ) } />
		</StyledStepGrid>
	);
}
