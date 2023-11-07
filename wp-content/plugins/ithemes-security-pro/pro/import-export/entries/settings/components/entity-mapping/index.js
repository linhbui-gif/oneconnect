/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import {
	EntitySelectControl,
	SelectControl,
} from '@ithemes/security-components';
import { CORE_STORE_NAME } from '@ithemes/security.packages.data';
import { STORE_NAME } from '@ithemes/security.import-export.data';
import './style.scss';

export function RoleMapping() {
	const { roles, targets, map } = useSelect(
		( select ) => ( {
			roles: select( CORE_STORE_NAME ).getRoles(),
			map: select( STORE_NAME ).getImportRoleMap(),
			targets: select(
				STORE_NAME
			).getImportExportRoleReplacementTargets(),
		} ),
		[]
	);
	const { editImportRoleMap } = useDispatch( STORE_NAME );

	if ( ! targets.length || ! roles ) {
		return null;
	}

	return (
		<EntityMapping
			title={ __( 'Replace Roles', 'it-l10n-ithemes-security-pro' ) }
			description={ __(
				'Choose the user roles that best match the roles from the export.',
				'it-l10n-ithemes-security-pro'
			) }
		>
			{ targets.map( ( target ) => (
				<RoleTarget
					key={ target.slug }
					target={ target }
					value={ map }
					onChange={ editImportRoleMap }
					roles={ roles }
				/>
			) ) }
		</EntityMapping>
	);
}

export function UserMapping() {
	const { targets, map } = useSelect(
		( select ) => ( {
			map: select( STORE_NAME ).getImportUserMap(),
			targets: select(
				STORE_NAME
			).getImportExportUserReplacementTargets(),
		} ),
		[]
	);
	const { editImportUserMap } = useDispatch( STORE_NAME );

	if ( ! targets.length ) {
		return null;
	}

	return (
		<EntityMapping
			title={ __( 'Replace Users', 'it-l10n-ithemes-security-pro' ) }
			description={ __(
				'Choose the users that best match the users from the export.',
				'it-l10n-ithemes-security-pro'
			) }
		>
			{ targets.map( ( target ) => (
				<UserTarget
					key={ target.id }
					target={ target }
					value={ map }
					onChange={ editImportUserMap }
				/>
			) ) }
		</EntityMapping>
	);
}

function EntityMapping( { title, description, children } ) {
	return (
		<fieldset className="itsec-import-export-entity-map">
			<legend className="itsec-import-export-entity-map__title">
				{ title }
			</legend>
			<p className="itsec-import-export-entity-map__description">
				{ description }
			</p>
			{ children }
		</fieldset>
	);
}

function RoleTarget( { target, value, onChange, roles } ) {
	const options = Object.entries( roles )
		.map( ( [ slug, role ] ) => ( {
			value: slug,
			label: role.label,
		} ) )
		.concat( { value: '', label: '' } );

	return (
		<SelectControl
			label={ target.label }
			value={
				value[ target.slug ] ??
				( roles[ target.slug ] ? target.slug : '' )
			}
			onChange={ ( newRole ) =>
				onChange( { ...value, [ target.slug ]: newRole } )
			}
			options={ options }
		/>
	);
}

function UserTarget( { target, value, onChange } ) {
	return (
		<EntitySelectControl
			value={ value[ target.id ] || 0 }
			onChange={ ( id ) => onChange( { ...value, [ target.id ]: id } ) }
			label={ sprintf(
				/* translators: 1. User's name, 2. User's email address. */
				__( '%1$ss (%2$s)', 'it-l10n-ithemes-security-pro' ),
				target.name,
				target.email
			) }
			path="/wp/v2/users"
			labelAttr="name"
		/>
	);
}
