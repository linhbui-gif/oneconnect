/**
 * External dependencies
 */
import { withTheme } from '@rjsf/core';
import classnames from 'classnames';
import { map } from 'lodash';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	Button,
	Card,
	CardBody,
	CardHeader,
	CheckboxControl,
	TextControl,
	Flex,
} from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Theme from '@ithemes/security-rjsf-theme';
import { useSet } from '@ithemes/security-hocs';
import { Accordion, FlexSpacer } from '@ithemes/security-components';
import './style.scss';

const SchemaForm = withTheme( Theme );

export default function ExportForm( {
	sources,
	isCreating,
	createExport,
	titleRequired,
	children,
} ) {
	const [ expanded, setExpanded ] = useState( '' );
	const [ title, setTitle ] = useState( '' );
	const [ options, setOptions ] = useState( {} );
	const [
		includedSources,
		addSource,
		removeSource,
		setIncludedSources,
	] = useSet( map( sources, 'slug' ) );
	useEffect( () => setIncludedSources( map( sources, 'slug' ) ), [
		sources,
	] );

	const panels = sources.map( ( source ) => ( {
		name: source.slug,
		title: source.title,
		description: source.description,
		// eslint-disable-next-line no-unused-vars
		render( { name, text, ...rest } ) {
			return (
				<SourcePanel
					source={ source }
					options={ options }
					setOptions={ setOptions }
					isIncluded={ includedSources.includes( source.slug ) }
					addSource={ addSource }
					removeSource={ removeSource }
					{ ...rest }
				/>
			);
		},
	} ) );

	const onSubmit = ( e ) => {
		e.preventDefault();
		createExport( {
			title,
			sources: includedSources,
			options,
		} );
	};

	return (
		<form onSubmit={ onSubmit }>
			<Card className="itsec-export-form">
				<CardHeader size="extraSmall">
					<CheckboxControl
						label={ __( 'Include All Data', 'it-l10n-ithemes-security-pro' ) }
						className="itsec-export-form__include-all"
						checked={ sources.every( ( source ) =>
							includedSources.includes( source.slug )
						) }
						onChange={ ( checked ) =>
							setIncludedSources(
								checked ? map( sources, 'slug' ) : []
							)
						}
					/>
					<FlexSpacer />
					<TextControl
						label={ __( 'Export Name', 'it-l10n-ithemes-security-pro' ) }
						className="itsec-export-form__name"
						value={ title }
						onChange={ setTitle }
						required={ titleRequired }
					/>
				</CardHeader>
				<CardBody>
					<Accordion
						className="itsec-export-form__sources"
						expanded={ expanded }
						setExpanded={ setExpanded }
						panels={ panels }
						isStyled
						allowNone
					/>
				</CardBody>
			</Card>
			<Flex>
				{ children }
				<FlexSpacer />
				<Button
					variant="primary"
					type="submit"
					disabled={ ! includedSources.length || isCreating }
					isBusy={ isCreating }
				>
					{ __( 'Create', 'it-l10n-ithemes-security-pro' ) }
				</Button>
			</Flex>
		</form>
	);
}

function SourcePanel( {
	source,
	options,
	setOptions,
	isIncluded,
	addSource,
	removeSource,
	...rest
} ) {
	return (
		<div { ...rest }>
			<div className="itsec-export-form__include-source">
				<CheckboxControl
					label={ sprintf(
						/* translators: 1. Export source name. */
						__( 'Include “%s” in Export', 'it-l10n-ithemes-security-pro' ),
						source.title
					) }
					checked={ isIncluded }
					onChange={ ( checked ) =>
						checked
							? addSource( source.slug )
							: removeSource( source.slug )
					}
				/>
			</div>
			{ source.options && isIncluded && (
				<SourceForm
					source={ source }
					options={ options }
					setOptions={ setOptions }
				/>
			) }
		</div>
	);
}

function SourceForm( { source, options, setOptions } ) {
	return (
		<SchemaForm
			tagName="div"
			className={ classnames(
				'rjsf',
				'itsec-export-form__options-form',
				`itsec-export-form__options-form--${ source.slug }`
			) }
			additionalMetaSchemas={ [
				require( 'ajv/lib/refs/json-schema-draft-04.json' ),
			] }
			schema={ source.options }
			uiSchema={ source.options.uiSchema }
			idPrefix={ `itsec_${ source.slug }` }
			formData={ options[ source.slug ] }
			onChange={ ( e ) => {
				setOptions( { ...options, [ source.slug ]: e.formData } );
			} }
		>
			<></>
		</SchemaForm>
	);
}
