/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { createSlotFill, SlotFillProvider, ToggleControl } from '@wordpress/components';
import { useMemo } from '@wordpress/element';
import { PluginArea } from '@wordpress/plugins';

/**
 * iThemes dependencies
 */
import { defaultTheme, Root, ShadowPortal } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { CORE_STORE_NAME } from '@ithemes/security.packages.data';

const { Slot: PasswordlessLoginProfileSlot, Fill: PasswordlessLoginProfileFill } = createSlotFill( 'PasswordlessLoginProfile' );

export { PasswordlessLoginProfileFill };

const StyledApp = styled.div`
	max-width: 50rem;
	box-sizing: border-box;
	*,
	::before,
	::after {
		box-sizing: border-box;
	}
`;

const styleSheetIds = [ 'wp-components-css' ];

export default function App( { userId, useShadow } ) {
	const { user, isSaving } = useSelect( ( select ) => ( {
		user: select( CORE_STORE_NAME ).getUser( userId ),
		isSaving: select( CORE_STORE_NAME ).isSavingUser( userId ),
	} ), [ userId ] );
	const { saveUser } = useDispatch( CORE_STORE_NAME );

	const fillProps = useMemo( () => ( { user, useShadow } ), [ user, useShadow ] );

	if ( ! user?.itsec_passwordless_login.available ) {
		return null;
	}

	const { itsec_passwordless_login: passwordlessLogin } = user;
	const controls = <Controls userId={ userId } saveUser={ saveUser } passwordlessLogin={ passwordlessLogin } isSaving={ isSaving } />;

	return (
		<Root theme={ defaultTheme }>
			<SlotFillProvider>
				<StyledApp>
					<PluginArea scope="ithemes-security" />
					<h2>{ __( 'Passwordless Login', 'it-l10n-ithemes-security-pro' ) }</h2>
					<p>{ getMethodsText( passwordlessLogin.available_methods ) }</p>
					{ useShadow ? <ShadowPortal children={ controls } styleSheetIds={ styleSheetIds } inherit /> : controls }
					{ passwordlessLogin.enabled && (
						<PasswordlessLoginProfileSlot fillProps={ fillProps } />
					) }
				</StyledApp>
			</SlotFillProvider>
		</Root>
	);
}

function Controls( { isSaving, passwordlessLogin, userId, saveUser } ) {
	const show2fa = passwordlessLogin.enabled && passwordlessLogin[ '2fa_used' ] && ! passwordlessLogin[ '2fa_enforced' ];

	return (
		<>
			<ToggleControl
				disabled={ isSaving }
				checked={ passwordlessLogin.enabled }
				onChange={ ( checked ) => saveUser( userId, { itsec_passwordless_login: { enabled: checked } }, true ) }
				label={ __( 'Enable Passwordless Login', 'it-l10n-ithemes-security-pro' ) }
			/>
			{ show2fa && (
				<ToggleControl
					disabled={ isSaving }
					checked={ passwordlessLogin[ '2fa_enabled' ] }
					onChange={ ( checked ) => saveUser( userId, { itsec_passwordless_login: { '2fa_enabled': checked } }, true ) }
					label={ __( 'Use Two-Factor during Passwordless Login', 'it-l10n-ithemes-security-pro' ) }
				/>
			) }
		</>
	);
}

function getMethodsText( methods ) {
	const global = __( 'Passwordless Login let’s you log in without needing to use your password.', 'it-l10n-ithemes-security-pro' ) + ' ';

	if ( methods.includes( 'magic' ) && methods.includes( 'webauthn' ) ) {
		return global + __( 'Instead, you can use a passkey built-in to your browser, or request an email with a Magic Link that will log you in with one click.', 'it-l10n-ithemes-security-pro' );
	}

	if ( methods.includes( 'magic' ) ) {
		return global + __( 'Instead, you’ll be emailed a Magic Link that will log you in with one click.', 'it-l10n-ithemes-security-pro' );
	}

	if ( methods.includes( 'webauthn' ) ) {
		return global + __( 'Instead, you can use a passkey built-in to your browser that will log you in with one click.', 'it-l10n-ithemes-security-pro' );
	}

	return null;
}
