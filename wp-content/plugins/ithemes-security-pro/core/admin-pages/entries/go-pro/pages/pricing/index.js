/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Flex } from '@wordpress/components';
import {
	commentAuthorName,
	currencyDollar,
	starFilled,
} from '@wordpress/icons';

/**
 * iThemes dependencies
 */
import { Callout, CalloutItem, Heading, PricingCard } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { Header, CardGrid } from '../../components';
import './style.scss';

export default function Pricing( { pricing } ) {
	return (
		<Flex className="itsec-go-pro-pricing" direction="column" gap={ 8 }>
			<Header
				title={ __( 'View Pricing & Plans', 'it-l10n-ithemes-security-pro' ) }
				subtitle={ __( 'The iThemes Security Pro plugin adds additional layers of protection for your WordPress website with performance in mind. Plus, iThemes Security Pro pricing is perfect for those on a budget.', 'it-l10n-ithemes-security-pro' ) }
			/>

			<CardGrid className="itsec-go-pro-pricing-grid">
				{ ( pricing || [] ).map( ( item, i ) => (
					<PricingCard key={ i } { ...item } />
				) ) }
			</CardGrid>

			<Heading level={ 2 } variant="dark" weight="heavy" text={ __( 'Why Buy from iThemes?', 'it-l10n-ithemes-security-pro' ) } />
			<Callout variant="secondary">
				<CalloutItem
					heading={ __( 'Fast, Friendly Support', 'it-l10n-ithemes-security-pro' ) }
					description={ __( 'We’ve been called “the friendliest support team in the WordPress world.” Most tickets are solved within one hour.', 'it-l10n-ithemes-security-pro' ) }
					icon={ commentAuthorName }
				/>
				<CalloutItem
					heading={ __( '30-Day Money Back Guarantee', 'it-l10n-ithemes-security-pro' ) }
					description={ __( 'We stand behind our products 100%. We offer a 30-day money-back guarantee with our refund policy.', 'it-l10n-ithemes-security-pro' ) }
					icon={ currencyDollar }
				/>
				<CalloutItem
					heading={ __( 'We’ve Been in Business Since 2008', 'it-l10n-ithemes-security-pro' ) }
					description={ __( 'Founded as one of the very first premium WordPress companies, we’re now one of the most trusted brands in WordPress.', 'it-l10n-ithemes-security-pro' ) }
					icon={ starFilled }
				/>
			</Callout>
		</Flex>
	);
}
