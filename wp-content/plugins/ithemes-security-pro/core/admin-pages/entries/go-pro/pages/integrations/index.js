/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Flex } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { CardGrid, Header, Integration } from '../../components';
import './style.scss';

export default function Integrations( { integrations } ) {
	return (
		<Flex className="itsec-go-pro-integrations" direction="column" gap={ 8 }>
			<Header title={ __( 'Additional Security Integrations', 'it-l10n-ithemes-security-pro' ) } subtitle={ __( 'Complete your WordPress security strategy with client reports and complete backups.', 'it-l10n-ithemes-security-pro' ) } />
			<CardGrid>
				{ integrations.map( ( integration, i ) => (
					<Integration key={ i } { ...integration } />
				) ) }
			</CardGrid>
		</Flex>
	);
}
