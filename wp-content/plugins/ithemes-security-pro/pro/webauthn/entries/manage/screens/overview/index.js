/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { Button, Card, CardBody, CardFooter } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * iThemes dependencies
 */
import { DiscreteProgressHeader, Heading, Text, SurfaceVariant, TextWeight, TextSize } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { LearnMore } from '../../components';

const StyledBody = styled( CardBody )`
	display: flex;
	flex-direction: column;
	gap: 1rem;
	margin-bottom: 3rem;
`;

export default function Overview( { onContinue, onManage } ) {
	return (
		<Card>
			<DiscreteProgressHeader surfaceVariant={ SurfaceVariant.PRIMARY_ACCENT }>
				<Heading
					level={ 2 }
					size={ TextSize.EXTRA_LARGE }
					weight={ TextWeight.HEAVY }
					text={ __( 'Great! You’ve activated and verified your passkey', 'it-l10n-ithemes-security-pro' ) }
				/>
			</DiscreteProgressHeader>
			<StyledBody>
				<Text
					as="p"
					text={ __( 'The next time you log in, look for the “Use Your Passkey” button to log in with one click!', 'it-l10n-ithemes-security-pro' ) }
				/>
				<LearnMore />
			</StyledBody>
			<CardFooter justify="space-between">
				<Button
					onClick={ onManage }
					variant="link"
					text={ __( 'Manage Passkeys', 'it-l10n-ithemes-security-pro' ) }
				/>
				<Button
					onClick={ onContinue }
					variant="primary"
					text={ __( 'Complete Registration', 'it-l10n-ithemes-security-pro' ) }
				/>
			</CardFooter>
		</Card>
	);
}
