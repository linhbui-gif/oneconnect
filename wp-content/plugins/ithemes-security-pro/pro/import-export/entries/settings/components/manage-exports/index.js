/**
 * External dependencies
 */
import { kebabCase, omit } from 'lodash';
import { saveAs } from 'file-saver';
import { Link, useRouteMatch } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { Button, Card, CardBody, CardHeader } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { gmdate } from '@wordpress/date';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { FlexSpacer, HelpList } from '@ithemes/security-components';
import { useSet, withNavigate } from '@ithemes/security-hocs';
import { HelpFill, PageHeader } from '@ithemes/security.pages.settings';
import { STORE_NAME } from '@ithemes/security.import-export.data';
import { ExportsList } from '../';
import './style.scss';

const waitFor = ( ms ) => new Promise( ( res ) => setTimeout( res, ms ) );

export default function ExportsCard() {
	const { url } = useRouteMatch();
	const [ isDeleting, setIsDeleting ] = useState( false );
	const [ isDownloading, setIsDownloading ] = useState( false );
	const [ selected, addSelected, removeSelected, setSelected ] = useSet();
	const { exports, sources } = useSelect(
		( select ) => ( {
			exports: select( STORE_NAME ).getExports(),
			sources: select( STORE_NAME ).getSources(),
		} ),
		[]
	);
	const { deleteExports } = useDispatch( STORE_NAME );
	const onDelete = async () => {
		setIsDeleting( true );
		await deleteExports( selected );
		setIsDeleting( false );
		setSelected( [] );
	};
	const onDownload = async () => {
		setIsDownloading( true );
		for ( const item of exports ) {
			if ( selected.includes( item.id ) ) {
				const blob = new window.Blob(
					[ JSON.stringify( omit( item, [ '_links' ] ) ) ],
					{
						type: 'text/plain;charset=utf-8',
					}
				);
				const name = item.metadata.title
					? kebabCase( item.metadata.title )
					: gmdate( 'y-m-d', item.metadata.exported_at );
				saveAs( blob, `itsec-export-${ name }.json` );
				await waitFor( 1000 );
			}
		}
		setIsDownloading( false );
	};

	return (
		<>
			<PageHeader
				title={ __( 'Manage Exports', 'it-l10n-ithemes-security-pro' ) }
				subtitle={ __(
					'Download, import, or create a new security settings export.',
					'it-l10n-ithemes-security-pro'
				) }
			/>
			<Card className="itsec-exports-card">
				<CardHeader size="extraSmall" isBorderless>
					<Button
						icon="download"
						text={ __( 'Download', 'it-l10n-ithemes-security-pro' ) }
						disabled={ selected.length === 0 || isDownloading }
						isBusy={ isDownloading }
						onClick={ onDownload }
					/>
					<Button
						icon="trash"
						text={ __( 'Delete', 'it-l10n-ithemes-security-pro' ) }
						disabled={ selected.length === 0 || isDeleting }
						isBusy={ isDeleting }
						onClick={ onDelete }
					/>
					<FlexSpacer />
					<Link
						to="/import"
						component={ withNavigate( Button ) }
						text={ __( 'Import', 'it-l10n-ithemes-security-pro' ) }
						icon="upload"
					/>
					<Link
						to={ url + '/create' }
						component={ withNavigate( Button ) }
						variant="secondary"
					>
						{ __( 'Create New Export', 'it-l10n-ithemes-security-pro' ) }
					</Link>
				</CardHeader>
				<CardBody>
					<ExportsList
						exports={ exports }
						sources={ sources }
						selected={ selected }
						addSelected={ addSelected }
						removeSelected={ removeSelected }
						setSelected={ setSelected }
					/>
				</CardBody>
			</Card>
			<HelpFill>
				<PageHeader title={ __( 'Manage Exports', 'it-l10n-ithemes-security-pro' ) } />
				<HelpList topic="export" />
			</HelpFill>
		</>
	);
}
