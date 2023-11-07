/**
 * External dependencies
 */
import { Link, useParams } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { ToolbarButton } from '@wordpress/components';

/**
 * Internal dependencies
 */
import {
	ONBOARD_STORE_NAME,
	ToolbarFill,
} from '@ithemes/security.pages.settings';
import { STORE_NAME } from '@ithemes/security.import-export.data';
import { useSingletonEffect, withNavigate } from '@ithemes/security-hocs';
import { Result } from '@ithemes/security-utils';
import { Import, Exports } from './pages';
import './style.scss';

export default function App() {
	useCompletionSteps();

	return (
		<>
			<Import />
			<Exports />
			<ToolbarFill>
				<ToolbarImportLink />
			</ToolbarFill>
		</>
	);
}

function ToolbarImportLink() {
	const { root } = useParams();

	if ( root !== 'onboard' ) {
		return null;
	}

	return (
		<Link
			to="/import"
			component={ withNavigate( ToolbarButton ) }
			text={ __( 'Import', 'it-l10n-ithemes-security-pro' ) }
			icon="upload"
		/>
	);
}

function useCompletionSteps() {
	const { registerCompletionStep } = useDispatch( ONBOARD_STORE_NAME );
	const { completeImport, resetImport, wpConnectReset } = useDispatch(
		STORE_NAME
	);
	useSingletonEffect( useCompletionSteps, () => {
		registerCompletionStep( {
			id: 'import',
			label: __( 'Complete Import', 'it-l10n-ithemes-security-pro' ),
			priority: 100,
			activeCallback( { root } ) {
				return root === 'import';
			},
			async callback() {
				const result = await completeImport();

				if ( result.type !== Result.SUCCESS ) {
					throw {
						code: result.error.getErrorCode(),
						message: result.error.getAllErrorMessages().join( ' ' ),
					};
				} else {
					await resetImport();
					await wpConnectReset();
				}
			},
			render: function ImportStep() {
				return <p>{ __( 'Import remaining export data.', 'it-l10n-ithemes-security-pro' ) }</p>;
			},
		} );
	} );
}
