/**
 * External dependencies
 */
import { find, map } from 'lodash';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { dateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import { CheckboxControl } from '@ithemes/security-components';
import './style.scss';

export default function ExportsList( {
	exports,
	sources,
	singleSelection,
	selected,
	addSelected,
	removeSelected,
	setSelected,
	isLoading = false,
} ) {
	const onItemChange = ( item ) => ( checked ) => {
		if ( singleSelection ) {
			setSelected( checked ? [ item ] : [] );
		} else if ( checked ) {
			addSelected( item );
		} else {
			removeSelected( item );
		}
	};

	const tableBody =
		exports.length > 0 ? (
			exports.map( ( item ) => (
				<tr key={ item.id }>
					<td className="itsec-exports-table__column-cb">
						<CheckboxControl
							label={ sprintf(
								/* translators: Export title */
								__( 'Select %s', 'it-l10n-ithemes-security-pro' ),
								item.metadata.title
							) }
							hideLabelFromVision
							className="itsec-exports-table__export-cb"
							checked={ selected.includes( item.id ) }
							onChange={ onItemChange( item.id ) }
						/>
					</td>
					<th className="itsec-exports-table__column-name">
						{ item.metadata.title }
					</th>
					<td className="itsec-exports-table__column-date">
						{ dateI18n( 'M j, Y', item.metadata.exported_at ) }
					</td>
					<td className="itsec-exports-table__column-content">
						{ Object.keys( item.sources )
							.map(
								( source ) =>
									find( sources, {
										slug: source,
									} )?.title || source
							)
							.join( ', ' ) }
					</td>
				</tr>
			) )
		) : (
			<tr>
				<td colSpan={ 4 }>{ __( 'No exports found.', 'it-l10n-ithemes-security-pro' ) }</td>
			</tr>
		);

	return (
		<table className="itsec-exports-list">
			<thead>
				<tr>
					<th className="itsec-exports-table__column-cb">
						{ ! singleSelection && (
							<CheckboxControl
								label={ __( 'Select All Exports', 'it-l10n-ithemes-security-pro' ) }
								hideLabelFromVision
								className="itsec-exports-table__export-cb"
								checked={
									exports.length > 0 &&
									exports.every( ( item ) =>
										selected.includes( item.id )
									)
								}
								onChange={ ( checked ) =>
									setSelected(
										checked ? map( exports, 'id' ) : []
									)
								}
							/>
						) }
					</th>
					<th className="itsec-exports-table__column-name">
						{ __( 'Name', 'it-l10n-ithemes-security-pro' ) }
					</th>
					<th className="itsec-exports-table__column-date">
						{ __( 'Date', 'it-l10n-ithemes-security-pro' ) }
					</th>
					<th className="itsec-exports-table__column-content">
						{ __( 'Content', 'it-l10n-ithemes-security-pro' ) }
					</th>
				</tr>
			</thead>
			<tbody>
				{ isLoading ? (
					<tr>
						<td
							colSpan={ 4 }
							className="itsec-exports-table__loading-message"
						>
							{ __( 'Loading exports…', 'it-l10n-ithemes-security-pro' ) }
						</td>
					</tr>
				) : (
					tableBody
				) }
			</tbody>
		</table>
	);
}
