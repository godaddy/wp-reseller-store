/**
 * 07 – Theme Compatibility
 *
 * Runs the same set of frontend checks against:
 *   - Twenty Twenty-Four (default block theme, WP 6.4+)
 *   - Storefront (classic WooCommerce theme)
 *
 * Storefront is installed via WP-CLI in the wp-env before() hook if not present.
 * Twenty Twenty-Four ships with WordPress so no install needed.
 */

const themes = [
	{ slug: 'twentytwentyfour', name: 'Twenty Twenty-Four' },
	{ slug: 'storefront',       name: 'Storefront' },
];

// Shared checks to run under each theme
function runFrontendChecks( getProductUrl, getDomainSearchUrl, getCartUrl ) {
	it( 'product page renders .rstore-pricing', () => {
		getProductUrl().then( ( url ) => {
			if ( ! url ) return cy.log( 'No product — skipping' );
			cy.visit( url );
			cy.get( '.rstore-pricing' ).should( 'exist' );
		} );
	} );

	it( 'product page renders the add-to-cart form', () => {
		getProductUrl().then( ( url ) => {
			if ( ! url ) return cy.log( 'No product — skipping' );
			cy.visit( url );
			cy.get( 'form.rstore-add-to-cart-form' ).should( 'exist' );
		} );
	} );

	it( '[rstore_domain_search] shortcode renders .rstore-domain-search', () => {
		getDomainSearchUrl().then( ( url ) => {
			cy.visit( url );
			cy.get( '.rstore-domain-search' ).should( 'exist' );
		} );
	} );

	it( '[rstore_cart_button] shortcode renders a cart link', () => {
		getCartUrl().then( ( url ) => {
			cy.visit( url );
			cy.get( '.rstore-view-cart a, .rstore-cart-button a' ).should( 'exist' );
		} );
	} );

	it( 'no fatal PHP errors on the product page', () => {
		getProductUrl().then( ( url ) => {
			if ( ! url ) return cy.log( 'No product — skipping' );
			cy.visit( url );
			cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
		} );
	} );
}

themes.forEach( ( { slug, name } ) => {
	describe( `07 – Theme Compat: ${ name }`, () => {
		let productUrl         = null;
		let domainSearchPageUrl = null;
		let cartPageUrl        = null;

		before( () => {
			cy.stubExternalApis();
			cy.loginAsAdmin();

			// Install Storefront if needed (no-op if already installed)
			if ( slug === 'storefront' ) {
				cy.exec( 'npx wp-env run cli -- wp theme install storefront --activate', {
					failOnNonZero: false,
				} );
			}

			// Activate the theme under test
			cy.activateTheme( slug );

			// Set up plugin (idempotent)
			cy.setupPlugin();

			// Prepare test pages
			cy.createPage( `${ name } - Domain Search`, '[rstore_domain_search]' )
				.then( ( url ) => { domainSearchPageUrl = url; } );

			cy.createPage( `${ name } - Cart Button`, '[rstore_cart_button]' )
				.then( ( url ) => { cartPageUrl = url; } );

			// Get first imported product URL
			cy.request( {
				url: '/wp-json/wp/v2/reseller_product?per_page=1',
				failOnStatusCode: false,
			} ).then( ( resp ) => {
				productUrl = resp.body[ 0 ]?.link ?? null;
			} );
		} );

		runFrontendChecks(
			() => cy.wrap( productUrl ),
			() => cy.wrap( domainSearchPageUrl ),
			() => cy.wrap( cartPageUrl )
		);
	} );
} );

// Restore Twenty Twenty-Four as the default theme after all theme tests
after( () => {
	cy.exec( 'npx wp-env run cli -- wp theme activate twentytwentyfour', { failOnNonZero: false } );
} );
