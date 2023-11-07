/**
 * External dependencies
 */
import { isPlainObject } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { useInstanceId } from '@wordpress/compose';
import { TextControl, VisuallyHidden } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { MODULES_STORE_NAME } from '@ithemes/security.packages.data';
import { RjsfFieldFill } from '@ithemes/security-rjsf-theme';
import { useAsync } from '@ithemes/security-hocs';
import { CheckboxControl } from '@ithemes/security-components';
import './style.scss';

const fetchPackages = () =>
	apiFetch( { path: '/ithemes-security/rpc/version-management/packages' } );

const kindLabels = {
	plugin: {
		label: __( 'Plugin Name', 'it-l10n-ithemes-security-pro' ),
		bulk: __( 'Select all plugins', 'it-l10n-ithemes-security-pro' ),
		caption: __(
			'Configure how plugins should be automatically updated.',
			'it-l10n-ithemes-security-pro'
		),
	},
	theme: {
		label: __( 'Theme Name', 'it-l10n-ithemes-security-pro' ),
		bulk: __( 'Select all themes', 'it-l10n-ithemes-security-pro' ),
		caption: __(
			'Configure how themes should be automatically updated.',
			'it-l10n-ithemes-security-pro'
		),
	},
};

export default function App() {
	const { status, value } = useAsync( fetchPackages );

	if ( status !== 'success' ) {
		return null;
	}

	return (
		<>
			<RjsfFieldFill name="itsec_version-management_plugin_automatic_updates">
				{ ( { formData } ) =>
					formData === 'custom' && (
						<Packages
							kind="plugin"
							packages={ value }
							labels={ kindLabels.plugin }
						/>
					)
				}
			</RjsfFieldFill>
			<RjsfFieldFill name="itsec_version-management_theme_automatic_updates">
				{ ( { formData } ) =>
					formData === 'custom' && (
						<Packages
							kind="theme"
							packages={ value }
							labels={ kindLabels.theme }
						/>
					)
				}
			</RjsfFieldFill>
		</>
	);
}

function Packages( { kind, packages, labels } ) {
	const instanceId = useInstanceId( Packages, 'itsec-vm-packages' );

	const setting = useSelect( ( select ) =>
		select( MODULES_STORE_NAME ).getEditedSetting(
			'version-management',
			'packages'
		)
	);
	const { editSetting } = useDispatch( MODULES_STORE_NAME );

	const [ checked, setChecked ] = useState( [] );

	if ( ! isPlainObject( setting ) ) {
		return null;
	}

	const filteredPackages = packages.filter( ( item ) => item.kind === kind );
	const onBulkCheck = ( bulkChecked ) => {
		if ( bulkChecked ) {
			setChecked( filteredPackages.map( ( item ) => item.id ) );
		} else {
			setChecked( [] );
		}
	};
	const onBulkAction = ( type, delay ) => {
		const next = { ...setting };
		checked.forEach(
			( item ) =>
				( next[ item ] = {
					...setting[ item ],
					type,
					delay: delay === undefined ? setting[ item ].delay : delay,
				} )
		);
		editSetting( 'version-management', 'packages', next );
	};

	return (
		<div className="itsec-vm-app">
			<table>
				<caption>{ labels.caption }</caption>
				<HeaderRow
					instanceId={ instanceId }
					isChecked={ checked.length === filteredPackages.length }
					setIsChecked={ onBulkCheck }
					onBulkAction={ onBulkAction }
					labels={ labels }
				/>
				<tbody>
					{ filteredPackages.map( ( item ) => (
						<PackageRow
							key={ item.id }
							instanceId={ instanceId }
							item={ item }
							value={ setting[ item.id ] }
							onChange={ ( change ) =>
								editSetting( 'version-management', 'packages', {
									...setting,
									[ item.id ]: change,
								} )
							}
							isChecked={ checked.includes( item.id ) }
							setIsChecked={ ( isChecked ) =>
								setChecked(
									isChecked
										? [ ...checked, item.id ]
										: checked.filter(
											( id ) => id !== item.id
										)
								)
							}
						/>
					) ) }
				</tbody>
			</table>
		</div>
	);
}

function HeaderRow( {
	instanceId,
	isChecked,
	setIsChecked,
	onBulkAction,
	labels,
} ) {
	const [ delay, setDelay ] = useState();
	const types = [
		{
			type: 'enabled',
			label: __( 'Enable', 'it-l10n-ithemes-security-pro' ),
			description: __(
				'Enable auto updates for all checked items.',
				'it-l10n-ithemes-security-pro'
			),
		},
		{
			type: 'disabled',
			label: __( 'Disable', 'it-l10n-ithemes-security-pro' ),
			description: __(
				'Disable auto updates for all checked items.',
				'it-l10n-ithemes-security-pro'
			),
		},
		{
			type: 'delay',
			label: __( 'Delay', 'it-l10n-ithemes-security-pro' ),
			description: __(
				'Delay auto updates for all checked items.',
				'it-l10n-ithemes-security-pro'
			),
		},
	];

	return (
		<thead>
			<tr className="itsec-vm-header">
				<th className="itsec-vm-column__bulk">
					<CheckboxControl
						checked={ isChecked }
						onChange={ setIsChecked }
						label={ labels.bulk }
						hideLabelFromVision
					/>
				</th>
				<th className="itsec-vm-column__name">{ labels.label }</th>

				{ types.map( ( { type, label, description } ) => (
					<th
						key={ type }
						className={ `itsec-vm-column__${ type }` }
						aria-label={ label }
					>
						<VisuallyHidden
							id={ `${ instanceId }__${ type }_help` }
						>
							{ description }
						</VisuallyHidden>
						<button
							type="button"
							onClick={ () =>
								onBulkAction(
									type,
									type === 'delay' ? delay : undefined
								)
							}
							className={ `itsec-vm-header__button itsec-vm-header__button--${ type }` }
							aria-describedby={ `${ instanceId }__${ type }_help` }
						>
							{ label }
						</button>
					</th>
				) ) }

				<th className="itsec-vm-column__days">
					<TextControl
						type="number"
						value={ delay }
						onChange={ setDelay }
						min={ 1 }
						placeholder={ __( '3 Days', 'it-l10n-ithemes-security-pro' ) }
						label={ __( 'Delay Update for Days', 'it-l10n-ithemes-security-pro' ) }
						hideLabelFromVision
					/>
				</th>
			</tr>
		</thead>
	);
}

function PackageRow( {
	item,
	instanceId,
	value = { type: 'enabled', delay: 3 },
	onChange,
	isChecked,
	setIsChecked,
} ) {
	const types = [
		{ type: 'enabled', label: __( 'Enabled', 'it-l10n-ithemes-security-pro' ) },
		{ type: 'disabled', label: __( 'Disabled', 'it-l10n-ithemes-security-pro' ) },
		{ type: 'delay', label: __( 'Delay', 'it-l10n-ithemes-security-pro' ) },
	];

	return (
		<tr
			className={ classnames(
				'itsec-vm-package',
				`itsec-vm-package--type-${ value.type }`
			) }
		>
			<td
				headers={ `itsec-vm-bulk-select itsec-vm-package__name-${ item.id }` }
				className="itsec-vm-package__bulk itsec-vm-column__bulk"
			>
				<CheckboxControl
					id={ `${ instanceId }__bulk-${ item.id }` }
					checked={ isChecked }
					onChange={ setIsChecked }
				/>
			</td>
			<th
				scope="row"
				id={ `${ instanceId }__name-${ item.id }` }
				className="itsec-vm-package__name itsec-vm-column__name"
			>
				<label htmlFor={ `${ instanceId }__bulk-${ item.id }` }>
					{ item.name }
				</label>
			</th>

			{ types.map( ( { type, label } ) => (
				<td
					key={ type }
					className={ `itsec-vm-package__type itsec-vm-package__type--${ type } itsec-vm-column__${ type }` }
				>
					<input
						type="radio"
						name={ `${ instanceId }[${ item.id }][type]` }
						id={ `${ instanceId }__type--${ type }-${ item.id }` }
						value={ type }
						checked={ value.type === type }
						onChange={ () => onChange( { ...value, type } ) }
					/>
					<label
						htmlFor={ `${ instanceId }__type--${ type }-${ item.id }` }
					>
						<span className="screen-reader-text">{ label }</span>
					</label>
				</td>
			) ) }

			<td className="itsec-vm-package__delay itsec-vm-column__days">
				<TextControl
					type="number"
					value={ value.delay }
					onChange={ ( delay ) => onChange( { ...value, delay } ) }
					min={ 1 }
					placeholder={ __( '3 Days', 'it-l10n-ithemes-security-pro' ) }
					label={ __( 'Delay Update for Days', 'it-l10n-ithemes-security-pro' ) }
					hideLabelFromVision
				/>
			</td>
		</tr>
	);
}
