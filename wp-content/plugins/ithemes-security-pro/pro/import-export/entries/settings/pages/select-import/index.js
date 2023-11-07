/**
 * External dependencies
 */
import { useRouteMatch } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { DropZone, FormFileUpload } from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import {
	PageHeader,
	SelectableCard,
	useNavigateTo,
} from '@ithemes/security.pages.settings';
import { STORE_NAME } from '@ithemes/security.import-export.data';
import './style.scss';

export default function SelectImport( { baseUrl } ) {
	const navigateTo = useNavigateTo();
	const { url } = useRouteMatch();
	const { validateExportFile, resetImport } = useDispatch( STORE_NAME );

	const onReceiveFiles = async ( fileList ) => {
		if ( fileList.length ) {
			await validateExportFile( fileList[ 0 ] );
			navigateTo( `${ baseUrl }/summary` );
		}
	};
	useEffect( () => {
		resetImport();
	}, [] );

	return (
		<>
			<PageHeader
				title={ __( 'Select Export', 'it-l10n-ithemes-security-pro' ) }
				description={ __(
					'Either upload an export file, or enter the URL of a website running iThemes Security.',
					'it-l10n-ithemes-security-pro'
				) }
			/>
			<div className="itsec-import-select-export-choices">
				<FormFileUpload
					onChange={ ( e ) => onReceiveFiles( e.target.files ) }
					accept="text/plain,application/json,application/zip"
					render={ ( { openFileDialog } ) => (
						<SelectableCard
							title={ __( 'Upload File', 'it-l10n-ithemes-security-pro' ) }
							description={ __(
								'Upload an iThemes Security export file from your computer.',
								'it-l10n-ithemes-security-pro'
							) }
							icon="upload"
							direction="vertical"
							onClick={ openFileDialog }
						/>
					) }
				/>
				<SelectableCard
					title={ __( 'WordPress Site', 'it-l10n-ithemes-security-pro' ) }
					description={ __(
						'Import from a WordPress site running iThemes Security.',
						'it-l10n-ithemes-security-pro'
					) }
					icon="wordpress"
					direction="vertical"
					to={ `${ url }/wordpress-connect` }
				/>
			</div>
			<DropZone
				className="itsec-import-export-dropzone"
				onFilesDrop={ onReceiveFiles }
			/>
		</>
	);
}
