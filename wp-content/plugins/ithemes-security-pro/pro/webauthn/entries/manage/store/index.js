/**
 * WordPress dependencies
 */
import { createReduxStore, register } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { controls } from '@ithemes/security.packages.data';
import * as actions from './actions';
import * as selectors from './selectors';
import * as resolvers from './resolvers';
import reducer from './reducer';

const STORE_NAME = 'ithemes-security/webauthn';
const store = createReduxStore( STORE_NAME, {
	actions,
	selectors,
	resolvers,
	reducer,
	controls,
} );

register( store );

export { store, STORE_NAME };
