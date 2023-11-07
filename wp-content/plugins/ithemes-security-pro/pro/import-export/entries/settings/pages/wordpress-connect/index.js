/**
 * External dependencies
 */
import { StringParam, useQueryParams } from 'use-query-params';
import { Link } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	Button,
	Card,
	CardHeader,
	CardBody,
	TextControl,
	Flex,
	createSlotFill,
} from '@wordpress/components';
import { createInterpolateElement, useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { addQueryArgs } from '@wordpress/url';
import { dateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import { PageHeader } from '@ithemes/security.pages.settings';
import { STORE_NAME } from '@ithemes/security.import-export.data';
import {
	FlexSpacer,
	MessageList,
	ResultSummary,
} from '@ithemes/security-components';
import { withNavigate } from '@ithemes/security-hocs';
import { WordPressConnectHeader } from '../../components';
import './style.scss';

// Flow:
// 1. Prompt for WordPress site.
// 2. Discover REST API root.
// 3. Check for App Passwords support.
// 4. Link user to Authorize Application page.
// 5. If rejected, show an error message.
// 6. If successful, rediscover the REST API root.
// 7. Check if the Exports route can be accessed.
// 8. Continue to Export selection.

const { Slot, Fill } = createSlotFill( 'ImportExportWordPressConnect' );

export default function WordPressConnect( { baseUrl } ) {
	const { step, url, discoveredApi, isExpired, isDeleting } = useSelect(
		( select ) => ( {
			step: select( STORE_NAME ).getWpConnectStep(),
			url: select( STORE_NAME ).getWpConnectSiteUrl(),
			discoveredApi: select( STORE_NAME ).getWpConnectDiscoveryResult(),
			isExpired: select( STORE_NAME ).areWpConnectCredentialsExpired(),
			isDeleting: select( STORE_NAME ).isDeletingWpConnectCredential(),
		} ),
		[]
	);
	const {
		wpConnectReset,
		wpConnectEnterWebsite,
		wpConnectDiscoverRestApi,
		wpConnectApplicationApproved,
		wpConnectApplicationRejected,
	} = useDispatch( STORE_NAME );
	const [ authQuery, setAuthQuery ] = useQueryParams( {
		user_login: StringParam,
		password: StringParam,
		site_url: StringParam,
		success: StringParam,
	} );

	useEffect( () => {
		if (
			authQuery.site_url &&
			authQuery.user_login &&
			authQuery.password
		) {
			wpConnectApplicationApproved( authQuery.site_url, {
				username: authQuery.user_login,
				password: authQuery.password,
			} );
		} else if ( authQuery.success === 'false' ) {
			wpConnectApplicationRejected();
		}

		setAuthQuery( {}, 'replace' );
	}, [ authQuery ] );

	const isInitialState = [ 'enterWebsite', 'discovering' ].includes( step );
	const onSubmit = ( e ) => {
		e.preventDefault();
		if ( step === 'enterWebsite' ) {
			wpConnectDiscoverRestApi( url );
		}
	};

	return (
		<>
			<PageHeader
				title={ __( 'Connect to WordPress', 'it-l10n-ithemes-security-pro' ) }
				description={ __(
					'Import from a WordPress site running iThemes Security.',
					'it-l10n-ithemes-security-pro'
				) }
			/>
			<form onSubmit={ onSubmit }>
				<Card className="itsec-import-wordpress-connect">
					{ ! isInitialState && (
						<CardHeader>
							<WordPressConnectHeader
								showSpinner={ step === 'appAuthed' }
							/>
						</CardHeader>
					) }
					<CardBody>
						{ isInitialState && (
							<WebsiteForm
								url={ url }
								setUrl={ wpConnectEnterWebsite }
								isDiscovering={ step === 'discovering' }
							/>
						) }
						<ResultSummary result={ discoveredApi } />
						<NoAppPasswordsMessage step={ step } />
						<RejectedMessage step={ step } />
						<ContinueAuthentication
							step={ step }
							isExpired={ isExpired }
						/>
						<AppAuthed step={ step } />
						<NoExportSupportMessage step={ step } />
						<GoToExportSelection
							step={ step }
							baseUrl={ baseUrl }
							isExpired={ isExpired }
						/>
					</CardBody>
				</Card>
				<Flex>
					{ ! isInitialState && (
						<Button
							text={ __( 'Start Again', 'it-l10n-ithemes-security-pro' ) }
							onClick={ wpConnectReset }
							isBusy={ isDeleting }
							disabled={ isDeleting }
							variant="secondary"
						/>
					) }
					<FlexSpacer />
					<Slot />
				</Flex>
			</form>
		</>
	);
}

function WebsiteForm( { url, setUrl, isDiscovering } ) {
	return (
		<>
			<TextControl
				type="url"
				label={ __( 'WordPress URL', 'it-l10n-ithemes-security-pro' ) }
				value={ url }
				onChange={ setUrl }
			/>
			<Fill>
				<Button
					text={ __( 'Connect', 'it-l10n-ithemes-security-pro' ) }
					type="submit"
					variant="primary"
					isBusy={ isDiscovering }
					disabled={ isDiscovering }
				/>
			</Fill>
		</>
	);
}

function ContinueAuthentication( { step, isExpired } ) {
	const { endpoint } = useSelect(
		( select ) => ( {
			endpoint: select(
				STORE_NAME
			).getWpConnectAppPasswordsAuthEndpoint(),
		} ),
		[]
	);

	if ( ! [ 'awaitingAuth', 'appRejected' ].includes( step ) && ! isExpired ) {
		return null;
	}

	const href = makeAuthEndpointUrl( endpoint );

	return (
		<>
			{ isExpired && (
				<MessageList
					type="warning"
					messages={ __(
						'Authentication expired. Please reauthorize iThemes Security.',
						'it-l10n-ithemes-security-pro'
					) }
				/>
			) }
			<MessageList
				type="info"
				title={ __( 'Continue Authentication', 'it-l10n-ithemes-security-pro' ) }
				messages={ createInterpolateElement(
					__(
						'<a>Authorize iThemes Security</a> to connect to your WordPress site to continue the import process.',
						'it-l10n-ithemes-security-pro'
					),
					{
						a: (
							// eslint-disable-next-line jsx-a11y/anchor-has-content
							<a href={ href } />
						),
					}
				) }
			/>
			<Fill>
				<Button
					text={ __( 'Authorize iThemes Security', 'it-l10n-ithemes-security-pro' ) }
					href={ href }
					variant="primary"
				/>
			</Fill>
		</>
	);
}

function AppAuthed( { step } ) {
	if ( step !== 'appAuthed' ) {
		return null;
	}

	return null;
}

function GoToExportSelection( { step, baseUrl, isExpired } ) {
	if ( isExpired || ! [ 'appAuthed', 'hasSupport' ].includes( step ) ) {
		return null;
	}

	return (
		<Fill>
			<Link
				to={ `${ baseUrl }/wordpress-select` }
				component={ withNavigate( Button ) }
				variant="primary"
				text={ __( 'Select Export', 'it-l10n-ithemes-security-pro' ) }
				disabled={ step === 'appAuthed' }
			/>
		</Fill>
	);
}

function NoAppPasswordsMessage( { step } ) {
	return (
		step === 'noAppPasswords' && (
			<MessageList
				type="error"
				messages={ __(
					'The website cannot be connected to because it does not support Application Passwords.',
					'it-l10n-ithemes-security-pro'
				) }
			/>
		)
	);
}

function RejectedMessage( { step } ) {
	return (
		step === 'appRejected' && (
			<MessageList
				type="error"
				messages={ __(
					'Application was not approved. You must click “I approve of this connection” to continue.',
					'it-l10n-ithemes-security-pro'
				) }
			/>
		)
	);
}

function NoExportSupportMessage( { step } ) {
	return (
		step === 'noSupport' && (
			<MessageList
				type="error"
				messages={ __(
					'The website must be running iThemes Security 7.1 or later.',
					'it-l10n-ithemes-security-pro'
				) }
			/>
		)
	);
}

function makeAuthEndpointUrl( authEndpoint ) {
	return addQueryArgs( authEndpoint, {
		app_name: sprintf(
			/* translators: 1. Website name. 2. Date */
			__( 'iThemes Security Import (%1$s) on %2$s', 'it-l10n-ithemes-security-pro' ),
			window.location.hostname,
			dateI18n( 'M j' )
		),
		// ns:url https://api.ithemes.com/app-passwords/ithemes-security-import
		app_id: '0fc6ff2d-d96f-5033-838f-90cc2bf48d9d',
		success_url: window.location.href,
	} );
}
