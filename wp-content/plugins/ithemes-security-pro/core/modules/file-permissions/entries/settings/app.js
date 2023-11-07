/**
 * External dependencies
 */
import { map } from 'lodash';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { TOOLS_STORE_NAME, ToolFill } from '@ithemes/security.pages.settings';
import './style.scss';

export default function App() {
	return (
		<>
			<ToolFill tool="check-file-permissions">
				<FilePermissions />
			</ToolFill>
		</>
	);
}

function FilePermissions() {
	const result = useSelect( ( select ) =>
		select( TOOLS_STORE_NAME ).getLastResult( 'check-file-permissions' )
	);

	if ( ! result || ! result.isSuccess() ) {
		return null;
	}

	const header = (
		<tr>
			<th>{ __( 'Relative Path', 'it-l10n-ithemes-security-pro' ) }</th>
			<th>{ __( 'Suggestion', 'it-l10n-ithemes-security-pro' ) }</th>
			<th>{ __( 'Value', 'it-l10n-ithemes-security-pro' ) }</th>
			<th>{ __( 'Result', 'it-l10n-ithemes-security-pro' ) }</th>
			<th>{ __( 'Status', 'it-l10n-ithemes-security-pro' ) }</th>
		</tr>
	);

	return (
		<div className="itsec-check-file-permissions-results">
			<table className="widefat striped">
				<thead>{ header }</thead>
				<tbody>
					{ map( result.data, ( row, path ) => (
						<tr key={ path }>
							<th>{ row.path }</th>
							<td>{ row.suggested }</td>
							<td>{ row.actual }</td>
							<td>
								{ row.actual === row.suggested
									? __( 'Ok', 'it-l10n-ithemes-security-pro' )
									: __( 'Warning', 'it-l10n-ithemes-security-pro' ) }
							</td>
							<td
								aria-hidden
								className={ `itsec-check-file-permissions-status itsec-check-file-permissions-status--${
									row.actual === row.suggested
										? 'ok'
										: 'warning'
								}` }
							>
								<div />
							</td>
						</tr>
					) ) }
				</tbody>
				<tfoot>{ header }</tfoot>
			</table>
		</div>
	);
}
