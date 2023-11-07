/**
 * External dependencies
 */
import { map, isPlainObject } from 'lodash';

/**
 * WordPress dependencies
 */
import {
	CheckboxControl,
	TextControl,
	TextareaControl,
	SelectControl,
	Button,
} from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { createInterpolateElement, Fragment } from '@wordpress/element';
import { useInstanceId } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import {
	PageHeader,
	PrimaryForm,
	PrimaryFormSection,
} from '@ithemes/security.pages.settings';
import { TextareaListControl } from '@ithemes/security-components';
import { UserRoleList } from '..';
import './style.scss';

const tags = [ 'a', 'i', 'b', 'h2', 'h3', 'h4', 'h5', 'h6', 'p' ];

export default function Notification( {
	notification,
	settings,
	onChange,
	usersAndRoles,
	isSaving,
	isDirty,
	onSubmit,
	onUndo,
	saveLabel = __( 'Save All', 'it-l10n-ithemes-security-pro' ),
	allowCleanSave = false,
	apiError,
} ) {
	const isEnabled = ! notification.optional || settings.enabled;

	return (
		<>
			<PageHeader
				title={ notification.l10n.label }
				description={ notification.l10n.description }
			/>
			<PrimaryForm
				saveLabel={ saveLabel }
				saveDisabled={ ! isDirty && ! allowCleanSave }
				isSaving={ isSaving }
				onSubmit={ onSubmit }
				apiError={ apiError }
				buttons={ [
					<Button
						key="undo"
						onClick={ onUndo }
						disabled={ ! isDirty }
					>
						{ __( 'Undo Changes', 'it-l10n-ithemes-security-pro' ) }
					</Button>,
				] }
			>
				{ notification.optional && (
					<CheckboxControl
						className="itsec-nc-notification__enabled"
						label={ __( 'Enabled', 'it-l10n-ithemes-security-pro' ) }
						checked={ settings.enabled }
						onChange={ ( enabled ) =>
							onChange( { ...settings, enabled } )
						}
					/>
				) }

				{ isEnabled && (
					<>
						<Customize
							notification={ notification }
							settings={ settings }
							onChange={ onChange }
						/>
						<Schedule
							notification={ notification }
							settings={ settings }
							onChange={ onChange }
						/>
						<Recipients
							notification={ notification }
							settings={ settings }
							onChange={ onChange }
							usersAndRoles={ usersAndRoles }
						/>
					</>
				) }
			</PrimaryForm>
		</>
	);
}

function Customize( { notification, settings, onChange } ) {
	const help = (
		<span>
			{ sprintf(
				/* translators: 1. Comma separated list of allowed HTML tags. */
				__(
					'You can use HTML in your message. Allowed HTML includes: %s.',
					'it-l10n-ithemes-security-pro'
				),
				tags.join( ', ' )
			) }{ ' ' }
			{ !! notification.tags &&
				createInterpolateElement(
					/* translators: 1. Example email tag. */
					__(
						'This notification supports email tags. Tags are formatted as follows <tag />.',
						'it-l10n-ithemes-security-pro'
					),
					{
						tag: <code>{ '{{ $tag_name }}' }</code>,
					}
				) }
		</span>
	);

	return (
		<PrimaryFormSection heading={ __( 'Customize', 'it-l10n-ithemes-security-pro' ) }>
			{ notification.subject_editable && (
				<TextControl
					label={ __( 'Subject', 'it-l10n-ithemes-security-pro' ) }
					placeholder={ notification.l10n.subject }
					value={ settings.subject || notification.l10n.subject }
					onChange={ ( subject ) =>
						onChange( { ...settings, subject } )
					}
				/>
			) }

			{ notification.message_editable && (
				<>
					<TextareaControl
						label={ __( 'Message', 'it-l10n-ithemes-security-pro' ) }
						placeholder={ notification.l10n.message }
						rows={ 10 }
						value={ settings.message || notification.l10n.message }
						onChange={ ( message ) =>
							onChange( { ...settings, message } )
						}
						help={ help }
					/>

					{ notification.tags && (
						<dl>
							{ map(
								notification.l10n.tags,
								( description, tag ) => (
									<Fragment key={ tag }>
										<dt>
											<code>{ tag }</code>
										</dt>
										<dd>{ description }</dd>
									</Fragment>
								)
							) }
						</dl>
					) }
				</>
			) }
		</PrimaryFormSection>
	);
}

function Schedule( { notification, settings, onChange } ) {
	const instanceId = useInstanceId(
		Recipients,
		'itsec-nc-notification__schedule'
	);

	if ( ! isPlainObject( notification.schedule ) ) {
		return null;
	}

	return (
		<PrimaryFormSection
			heading={
				<label htmlFor={ instanceId }>
					{ __( 'Schedule', 'it-l10n-ithemes-security-pro' ) }
				</label>
			}
		>
			<SelectControl
				id={ instanceId }
				options={ notification.l10n.schedule }
				value={ settings.schedule }
				onChange={ ( schedule ) =>
					onChange( { ...settings, schedule } )
				}
			/>
		</PrimaryFormSection>
	);
}

function Recipients( { notification, settings, onChange, usersAndRoles } ) {
	const instanceId = useInstanceId(
		Recipients,
		'itsec-nc-notification__recipients'
	);

	switch ( notification.recipient ) {
		case 'user':
			return (
				<PrimaryFormSection
					className="itsec-nc-notification__recipients"
					heading={ __( 'Recipient', 'it-l10n-ithemes-security-pro' ) }
				>
					{ __( 'Site Users', 'it-l10n-ithemes-security-pro' ) }
				</PrimaryFormSection>
			);
		case 'admin':
			return (
				<PrimaryFormSection
					className="itsec-nc-notification__recipients"
					heading={ __( 'Recipient', 'it-l10n-ithemes-security-pro' ) }
				>
					{ __( 'Admin Emails', 'it-l10n-ithemes-security-pro' ) }
				</PrimaryFormSection>
			);
		case 'per-use':
			return (
				<PrimaryFormSection
					className="itsec-nc-notification__recipients"
					heading={ __( 'Recipient', 'it-l10n-ithemes-security-pro' ) }
				>
					{ __( 'Specified when sending', 'it-l10n-ithemes-security-pro' ) }
				</PrimaryFormSection>
			);
		case 'email-list':
			return (
				<PrimaryFormSection
					heading={
						<label htmlFor={ instanceId }>
							{ __( 'Recipient', 'it-l10n-ithemes-security-pro' ) }
						</label>
					}
				>
					<TextareaListControl
						id={ instanceId }
						help={ __(
							'The email address(es) this notification will be sent to. One address per line.',
							'it-l10n-ithemes-security-pro'
						) }
						value={ settings.email_list }
						onChange={ ( emailList ) =>
							onChange( { ...settings, email_list: emailList } )
						}
					/>
				</PrimaryFormSection>
			);
		case 'user-list':
			return (
				<PrimaryFormSection
					className="itsec-nc-notification__recipients itsec-nc-notification__recipients--user-list"
					heading={
						<label htmlFor={ instanceId }>
							{ __( 'Recipient', 'it-l10n-ithemes-security-pro' ) }
						</label>
					}
				>
					<SelectControl
						value={ settings.recipient_type }
						onChange={ ( recipientType ) =>
							onChange( {
								...settings,
								recipient_type: recipientType,
							} )
						}
						id={ instanceId }
						options={ [
							{
								value: 'default',
								label: __( 'Default Recipients', 'it-l10n-ithemes-security-pro' ),
							},
							{
								value: 'custom',
								label: __( 'Custom', 'it-l10n-ithemes-security-pro' ),
							},
						] }
					/>

					{ settings.recipient_type === 'custom' && (
						<UserRoleList
							help={ __(
								'Select which users should be emailed.',
								'it-l10n-ithemes-security-pro'
							) }
							value={ settings.user_list || [] }
							onChange={ ( userList ) =>
								onChange( { ...settings, user_list: userList } )
							}
							usersAndRoles={ usersAndRoles }
						/>
					) }
				</PrimaryFormSection>
			);
	}
}
