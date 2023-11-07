/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useCallback } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { RjsfFieldFill } from '@ithemes/security-rjsf-theme';
import { useAsync } from '@ithemes/security-hocs';
import { ErrorList } from '@ithemes/security-components';
import './style.scss';

function statusCheck() {
	return apiFetch( {
		path: 'ithemes-security/rpc/geolocation/maxmind-db-check',
	} );
}

function DownloadDB( { apiKey } ) {
	const download = useCallback(
		() =>
			apiFetch( {
				method: 'POST',
				path: 'ithemes-security/rpc/geolocation/maxmind-db-download',
				data: { api_key: apiKey },
			} ),
		[ apiKey ]
	);
	const {
		status: downloadStatus,
		execute: executeDownload,
		value: downloaded,
		error: downloadError,
	} = useAsync( download, false );
	const { status: checkStatus, value: check } = useAsync( statusCheck );
	const isDownloaded =
		downloadStatus === 'success' ||
		( checkStatus === 'success' && check.available );

	return (
		<div className="itsec-geolocation-maxmind-download">
			<Button
				variant="secondary"
				disabled={ ! apiKey.length }
				onClick={ executeDownload }
				isBusy={ downloadStatus === 'pending' }
			>
				{ __( 'Download DB', 'it-l10n-ithemes-security-pro' ) }
			</Button>
			<span>
				{ __( 'The download may take a few moments (27MB).', 'it-l10n-ithemes-security-pro' ) }
			</span>

			{ isDownloaded && (
				<>
					<p>
						{ __(
							'The MaxMind DB has been downloaded. You may want to exclude the following directory from your backups.',
							'it-l10n-ithemes-security-pro'
						) }
					</p>
					<p>
						<code>{ downloaded?.path || check?.path }</code>
					</p>
				</>
			) }
			{ downloadStatus === 'error' && (
				<ErrorList apiError={ downloadError } />
			) }
		</div>
	);
}

export default function App() {
	return (
		<>
			<RjsfFieldFill name="itsec_geolocation_maxmind_lite_key">
				{ ( { formData } ) => <DownloadDB apiKey={ formData || '' } /> }
			</RjsfFieldFill>
		</>
	);
}
