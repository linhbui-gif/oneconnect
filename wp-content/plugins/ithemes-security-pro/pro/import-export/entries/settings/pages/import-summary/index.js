/**
 * External dependencies
 */
import { Link } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import {
	Button,
	Card,
	CardHeader,
	CardBody,
	Flex,
} from '@wordpress/components';
import { dateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import { STORE_NAME } from '@ithemes/security.import-export.data';
import { PageHeader, useNavigation } from '@ithemes/security.pages.settings';
import {
	CheckboxGroupControl,
	FlexSpacer,
	ResultSummary,
} from '@ithemes/security-components';
import { withNavigate } from '@ithemes/security-hocs';
import {
	RoleMapping,
	UserMapping,
	WordPressConnectHeader,
} from '../../components';
import './style.scss';

export default function ImportSummary( { baseUrl } ) {
	const { goNext } = useNavigation();
	const { result, data, sources, exportSource, selectedSources } = useSelect(
		( select ) => ( {
			result: select( STORE_NAME ).getImportExportValidationResult(),
			data: select( STORE_NAME ).getImportExportData(),
			sources: select( STORE_NAME ).getSources(),
			exportSource: select( STORE_NAME ).getImportExportSource(),
			selectedSources: select( STORE_NAME ).getImportSources(),
		} ),
		[]
	);
	const {
		applyExportData,
		editImportSources: setSelectedSources,
	} = useDispatch( STORE_NAME );
	useEffect( () => {
		setSelectedSources( Object.keys( data?.sources || {} ) );
	}, [ data ] );
	const sourceOptions = sources
		.filter( ( source ) => data?.sources[ source.slug ] )
		.map( ( source ) => ( {
			value: source.slug,
			label: source.title,
			help: source.description,
		} ) );
	const [ isApplying, setIsApplying ] = useState( false );
	const onContinue = async () => {
		setIsApplying( true );
		await applyExportData();
		setIsApplying( false );
		goNext();
	};

	return (
		<>
			<PageHeader title={ __( 'Import Summary', 'it-l10n-ithemes-security-pro' ) } />
			<ResultSummary result={ result } />
			<Card>
				{ exportSource === 'connect' && (
					<CardHeader>
						<WordPressConnectHeader isConnected />
					</CardHeader>
				) }
				<ExportDetails data={ data } source={ exportSource } />
			</Card>
			{ data && (
				<Card>
					<CardBody>
						<CheckboxGroupControl
							label={ __( 'Export Data', 'it-l10n-ithemes-security-pro' ) }
							help={ __(
								'Choose what iThemes Security info you’d like to import.',
								'it-l10n-ithemes-security-pro'
							) }
							value={ selectedSources }
							onChange={ setSelectedSources }
							options={ sourceOptions }
							className="itsec-import-summary-select-data"
						/>
						{ selectedSources.length > 0 && (
							<>
								<UserMapping />
								<RoleMapping />
							</>
						) }
					</CardBody>
				</Card>
			) }
			<Flex>
				<Link
					to={ getBackLink( baseUrl, exportSource ) }
					variant="secondary"
					component={ withNavigate( Button ) }
					text={ __( 'Back', 'it-l10n-ithemes-security-pro' ) }
				/>
				<FlexSpacer />
				<Button
					onClick={ onContinue }
					variant="primary"
					isBusy={ isApplying }
					disabled={ isApplying || ! selectedSources.length }
					text={ __( 'Continue', 'it-l10n-ithemes-security-pro' ) }
				/>
			</Flex>
		</>
	);
}

function ExportDetails( { data, source } ) {
	let title = data.metadata.title;

	if ( ! title && source !== 'connect' ) {
		title = sprintf(
			/* translators: 1. URL */
			__( 'Exported from %s', 'it-l10n-ithemes-security-pro' ),
			data.metadata.home_url
		);
	}

	return (
		<CardBody className="itsec-import-summary-export-details">
			{ title && <h3>{ data.metadata.title }</h3> }
			<span>
				{ sprintf(
					/* translators: 1. Date */
					__( 'Exported on %s', 'it-l10n-ithemes-security-pro' ),
					dateI18n( 'M j, Y', data.metadata.exported_at )
				) }
			</span>
			<span>
				{ sprintf(
					/* translators: 1. Version number */
					__( 'Version %s', 'it-l10n-ithemes-security-pro' ),
					data.metadata.version
				) }
			</span>
		</CardBody>
	);
}

function getBackLink( baseUrl, source ) {
	switch ( source ) {
		case 'connect':
			return baseUrl + '/wordpress-select';
		default:
			return baseUrl;
	}
}
