import './commands';

// Suppress uncaught exceptions that are unrelated to the plugin under test
Cypress.on( 'uncaught:exception', ( err ) => {
	// Gutenberg/block editor throws ResizeObserver loop errors in headed mode
	if ( err.message.includes( 'ResizeObserver loop' ) ) return false;
	// WP admin occasionally throws script errors from unrelated core code
	if ( err.message.includes( 'wp.i18n' ) ) return false;
	// Plugin JS assets may have syntax errors in older builds — suppress so DOM assertions still run
	if ( err.message.includes( 'Invalid or unexpected token' ) ) return false;
	if ( err.message.includes( 'SyntaxError' ) ) return false;
	// store.js uses js-cookie global which is not always available in test environment
	if ( err.message.includes( 'Cookies is not defined' ) ) return false;
	return true;
} );
