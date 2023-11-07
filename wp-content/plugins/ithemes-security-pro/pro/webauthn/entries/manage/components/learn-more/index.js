/**
 * WordPress dependencies
 */
import { external } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';

/**
 * iThemes dependencies
 */
import { Text, TextSize, TextVariant } from '@ithemes/ui';

export default function LearnMore( { textSize = TextSize.SMALL } ) {
	return (
		<a href="https://help.ithemes.com/hc/en-us/articles/8835770116763/" target="_blank" rel="noreferrer">
			<Text
				icon={ external }
				iconPosition="right"
				iconSize={ 16 }
				variant={ TextVariant.ACCENT }
				size={ textSize }
				text={ __( 'Learn more about passkeys', 'it-l10n-ithemes-security-pro' ) }
			/>
		</a>
	);
}
