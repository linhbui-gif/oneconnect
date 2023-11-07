/**
 * External dependencies
 */
import { Link } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { createInterpolateElement } from '@wordpress/element';

/**
 * Internal dependencies
 */
import {
	HelpList,
	MessageList,
	ResultSummary,
} from '@ithemes/security-components';
import { HelpFill, PageHeader } from '@ithemes/security.pages.settings';
import { STORE_NAME } from '@ithemes/security.import-export.data';
import { ExportForm } from '../../components';

export default function CreateExport() {
	const { sources, isCreating, lastResult } = useSelect(
		( select ) => ( {
			sources: select( STORE_NAME ).getSources(),
			isCreating: select( STORE_NAME ).isCreatingExport(),
			lastResult: select( STORE_NAME ).getLastCreatedExportResult(),
		} ),
		[]
	);
	const { createExport } = useDispatch( STORE_NAME );

	return (
		<>
			<PageHeader
				title={ __( 'Create New Export', 'it-l10n-ithemes-security-pro' ) }
				subtitle={ __(
					'Choose which elements of iThemes Security to include in the export.',
					'it-l10n-ithemes-security-pro'
				) }
			/>
			{ lastResult?.isSuccess() && (
				<MessageList
					type="success"
					hasBorder
					messages={ [
						createInterpolateElement(
							__(
								'Export created. <a>Back to list</a>.',
								'it-l10n-ithemes-security-pro'
							),
							{ a: <Link to="/settings/exports" /> }
						),
					] }
				/>
			) }
			<ResultSummary result={ lastResult } hasBorder />
			{ sources.length > 0 && (
				<ExportForm
					sources={ sources }
					isCreating={ isCreating }
					createExport={ createExport }
					titleRequired
				/>
			) }
			<HelpFill>
				<PageHeader title={ __( 'Create New Export', 'it-l10n-ithemes-security-pro' ) } />
				<HelpList topic="create-export" fallback="export" />
			</HelpFill>
		</>
	);
}
