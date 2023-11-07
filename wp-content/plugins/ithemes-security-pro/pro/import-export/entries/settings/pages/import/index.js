/**
 * External dependencies
 */
import { Switch, Route, useRouteMatch } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Page, HelpFill, PageHeader } from '@ithemes/security.pages.settings';
import { HelpList } from '@ithemes/security-components';
import {
	SelectImport,
	WordPressConnect,
	WordPressSelect,
	WordPressCreate,
	ImportSummary,
} from '../';

export default function Import() {
	return (
		<Page
			id="select-export"
			title={ __( 'Select Export', 'it-l10n-ithemes-security-pro' ) }
			roots={ [ 'import' ] }
			priority={ 0 }
		>
			{ () => <Routes /> }
		</Page>
	);
}

function Routes() {
	const { path, url } = useRouteMatch();

	return (
		<>
			<Switch>
				<Route
					path={ `${ path }/wordpress-create` }
					render={ () => <WordPressCreate baseUrl={ url } /> }
				/>
				<Route
					path={ `${ path }/wordpress-select` }
					render={ () => <WordPressSelect baseUrl={ url } /> }
				/>
				<Route
					path={ `${ path }/wordpress-connect` }
					render={ () => <WordPressConnect baseUrl={ url } /> }
				/>
				<Route
					path={ `${ path }/summary` }
					render={ () => <ImportSummary baseUrl={ url } /> }
				/>
				<Route
					path={ path }
					render={ () => <SelectImport baseUrl={ url } /> }
				/>
			</Switch>
			<HelpFill>
				<PageHeader title={ __( 'Import', 'it-l10n-ithemes-security-pro' ) } />
				<HelpList topic="import" />
			</HelpFill>
		</>
	);
}
