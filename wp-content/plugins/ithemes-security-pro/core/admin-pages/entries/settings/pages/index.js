/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Page } from '../page-registration';
import SiteType from './site-type';
import Modules from './modules';
import Configure from './configure';
import Tools from './tools';
import SecureSite from './secure-site';

export default function Pages() {
	return (
		<>
			<Page
				id="site-type"
				title={ __( 'Site Type', 'it-l10n-ithemes-security-pro' ) }
				priority={ 0 }
				roots={ [ 'onboard' ] }
			>
				{ () => <SiteType /> }
			</Page>
			<Page
				id="modules"
				title={ __( 'Features', 'it-l10n-ithemes-security-pro' ) }
				icon="shield"
				priority={ 5 }
				roots={ [ 'onboard', 'settings', 'import' ] }
			>
				{ () => <Modules /> }
			</Page>
			<Page
				id="configure"
				title={ __( 'Configure', 'it-l10n-ithemes-security-pro' ) }
				icon="admin-settings"
				priority={ 15 }
				roots={ [ 'onboard', 'settings', 'import' ] }
				ignore={ [ '/advanced/' ] }
			>
				{ () => <Configure /> }
			</Page>
			<Page
				id="secure-site"
				title={ __( 'Secure Site', 'it-l10n-ithemes-security-pro' ) }
				priority={ 100 }
				roots={ [ 'onboard', 'import' ] }
			>
				{ () => <SecureSite /> }
			</Page>
			<Page
				id="tools"
				title={ __( 'Tools', 'it-l10n-ithemes-security-pro' ) }
				icon="admin-tools"
				priority={ 80 }
				location="advanced"
				roots={ [ 'settings' ] }
			>
				{ () => <Tools /> }
			</Page>
		</>
	);
}

export { default as Onboard } from './onboard';
export { default as Settings } from './settings';
export { default as Import } from './import';
