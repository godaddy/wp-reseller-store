import './commands';

// Suppress uncaught exceptions that are unrelated to the plugin under test
Cypress.on( 'uncaught:exception', ( err ) => {
	// Gutenberg/block editor throws ResizeObserver loop errors in headed mode
	if ( err.message.includes( 'ResizeObserver loop' ) ) return false;
	// WP admin occasionally throws script errors from unrelated core code
	if ( err.message.includes( 'wp.i18n' ) ) return false;
	return true;
} );
