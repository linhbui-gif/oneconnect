/**
 * WordPress dependencies
 */
import { Card, CardBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * iThemes dependencies
 */
import { MessageList } from '@ithemes/ui';

export default function NotAvailable() {
	return (
		<Card>
			<CardBody>
				<MessageList type="warning" messages={ [ __( 'Your browser does not support passkeys.', 'it-l10n-ithemes-security-pro' ) ] } />
			</CardBody>
		</Card>
	);
}
