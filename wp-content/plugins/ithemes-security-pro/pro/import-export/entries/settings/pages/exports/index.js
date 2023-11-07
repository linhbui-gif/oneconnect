/**
 * External dependencies
 */
import { Route, Switch, useRouteMatch } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Page } from '@ithemes/security.pages.settings';
import { ManageExports } from '../../components';
import { CreateExport } from '../';

export default function Exports() {
	return (
		<Page
			id="exports"
			title={ __( 'Exports', 'it-l10n-ithemes-security-pro' ) }
			icon="download"
			location="advanced"
		>
			{ () => <Content /> }
		</Page>
	);
}

function Content() {
	const { path } = useRouteMatch();

	return (
		<Switch>
			<Route path={ path + '/create' } component={ CreateExport } />
			<Route component={ ManageExports } />
		</Switch>
	);
}
