/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { dateI18n } from '@wordpress/date';
import { Button } from '@wordpress/components';
import { useCallback } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { useAsync } from '@ithemes/security-hocs';
import { Result } from '@ithemes/security-utils';
import { getPasswordStrength, getTwoFactor, useNoticeCreator } from './utils';

export default function UserInfo( { card, user } ) {
	return (
		<section className="itsec-card-security-profile__user-info">
			<table>
				<tbody>
					<tr>
						<th>{ __( 'Role', 'it-l10n-ithemes-security-pro' ) }</th>
						<td>{ user.role }</td>
					</tr>
					<tr>
						<th>{ __( 'Password Strength', 'it-l10n-ithemes-security-pro' ) }</th>
						<td>
							<span
								className={ classnames(
									'itsec-card-security-profile__password-strength',
									`itsec-card-security-profile__password-strength--${
										getPasswordStrength(
											user.password_strength
										)[ 0 ]
									}`
								) }
							>
								{
									getPasswordStrength(
										user.password_strength
									)[ 1 ]
								}
							</span>
						</td>
					</tr>
					{ user.password_last_changed && (
						<tr>
							<th>{ __( 'Password Age', 'it-l10n-ithemes-security-pro' ) }</th>
							<td>
								<span
									title={ dateI18n(
										'M d, Y g:s A',
										user.password_last_changed.time
									) }
								>
									{ user.password_last_changed.diff }
								</span>
							</td>
						</tr>
					) }
					<tr>
						<th>{ __( 'Two-Factor', 'it-l10n-ithemes-security-pro' ) }</th>
						<td>{ getTwoFactor( user.two_factor )[ 1 ] }</td>
					</tr>
					{ user.last_active && (
						<tr>
							<th>{ __( 'Last Seen', 'it-l10n-ithemes-security-pro' ) }</th>
							<td>
								<span
									title={ dateI18n(
										'M d, Y g:s A',
										user.last_active.time
									) }
								>
									{ user.last_active.diff }
								</span>
							</td>
						</tr>
					) }
				</tbody>
			</table>
			<UserActions card={ card } user={ user } />
		</section>
	);
}

function UserActions( { card, user } ) {
	return (
		<div className="itsec-card-security-profile__user-actions">
			<TwoFactorReminder card={ card } user={ user } />
			<ForceLogout card={ card } user={ user } />
		</div>
	);
}

function TwoFactorReminder( { card, user } ) {
	const link =
		card._links?.[ 'ithemes-security:send-2fa-reminder' ]?.[ 0 ]?.href;

	const { execute, status, value, error } = useAsync(
		useCallback( () =>
			apiFetch( {
				url: link.replace( '{user_id}', user.id ),
				parse: false,
				method: 'POST',
			} )
				.then( Result.fromResponse )
				.catch(
					async ( response ) =>
						throw ( await Result.fromResponse( response ) )
				)
		),
		false
	);
	useNoticeCreator( status, value, error );

	if (
		! link ||
		( user.two_factor !== 'enforced-not-configured' &&
			user.two_factor !== 'not-enabled' )
	) {
		return null;
	}

	return (
		<Button
			isSmall
			variant="primary"
			isBusy={ status === 'pending' }
			onClick={ execute }
		>
			{ __( 'Send Two-Factor Reminder', 'it-l10n-ithemes-security-pro' ) }
		</Button>
	);
}

function ForceLogout( { card, user } ) {
	const link = card._links?.[ 'ithemes-security:logout' ]?.[ 0 ]?.href;

	const { execute, status, value, error } = useAsync(
		useCallback( () =>
			apiFetch( {
				url: link.replace( '{user_id}', user.id ),
				parse: false,
				method: 'POST',
			} )
				.then( Result.fromResponse )
				.catch(
					async ( response ) =>
						throw ( await Result.fromResponse( response ) )
				)
		),
		false
	);
	useNoticeCreator( status, value, error );

	if ( ! link ) {
		return null;
	}

	return (
		<Button
			isSmall
			variant="secondary"
			isBusy={ status === 'pending' }
			onClick={ execute }
		>
			{ __( 'Force Logout', 'it-l10n-ithemes-security-pro' ) }
		</Button>
	);
}
