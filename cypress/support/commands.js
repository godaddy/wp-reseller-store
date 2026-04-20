// ─── Login ────────────────────────────────────────────────────────────────────

/**
 * Log in to WordPress admin using cy.session() so the cookie is cached
 * and reused across specs without re-visiting the login page every time.
 */
Cypress.Commands.add( 'loginAsAdmin', () => {
	cy.session( 'admin-session', () => {
		cy.visit( '/wp-login.php' );
		cy.get( '#user_login' ).clear().type( Cypress.env( 'wpUser' ) );
		cy.get( '#user_pass' ).clear().type( Cypress.env( 'wpPass' ) );
		cy.get( '#wp-submit' ).click();
		cy.url().should( 'include', '/wp-admin' );
	} );
} );

// ─── External API stubs ───────────────────────────────────────────────────────

/**
 * Intercept all outbound GoDaddy API calls and return fixture data.
 * Call this before visiting any page that might trigger external requests.
 */
Cypress.Commands.add( 'stubExternalApis', () => {
	cy.intercept( 'GET', '**/api/v1/catalog/*/products**', {
		fixture: 'products.json',
	} ).as( 'catalogApi' );

	cy.intercept( 'GET', '**/api/v1/settings**', {
		fixture: 'branding.json',
	} ).as( 'brandingApi' );

	cy.intercept( 'POST', '**/api/v1/cart/**', {
		statusCode: 200,
		body: {},
	} ).as( 'cartApi' );
} );

// ─── Plugin setup ─────────────────────────────────────────────────────────────

/**
 * Set up the plugin by calling the rstore_install AJAX action directly,
 * using the nonce that the setup page localises into the page.
 *
 * This mimics what happens after the GoDaddy OAuth redirect completes —
 * skipping the external OAuth flow entirely.
 *
 * @param {number} plId Private Label ID (default from Cypress.env('plId'))
 */
Cypress.Commands.add( 'setupPlugin', ( plId = Cypress.env( 'plId' ) ) => {
	cy.stubExternalApis();
	cy.loginAsAdmin();

	// Visit setup page to read the localised nonce
	cy.visit( '/wp-admin/admin.php?page=reseller-store-setup' );

	cy.window().then( ( win ) => {
		const nonce = win.rstore_admin_setup?.install_nonce;

		// If no nonce, the plugin may already be set up — skip
		if ( ! nonce ) return;

		cy.request( {
			method: 'POST',
			url: '/wp-admin/admin-ajax.php',
			form: true,
			body: {
				action: 'rstore_install',
				nonce,
				pl_id: plId,
			},
		} ).its( 'status' ).should( 'eq', 200 );
	} );
} );

// ─── Theme switching ──────────────────────────────────────────────────────────

/**
 * Activate a WordPress theme by slug via the REST API.
 *
 * @param {string} slug Theme slug, e.g. 'twentytwentyfour' or 'storefront'
 */
Cypress.Commands.add( 'activateTheme', ( slug ) => {
	cy.request( {
		method: 'POST',
		url: '/wp-json/wp/v2/settings',
		auth: { user: Cypress.env( 'wpUser' ), pass: Cypress.env( 'wpPass' ) },
		body: { stylesheet: slug },
	} ).its( 'status' ).should( 'eq', 200 );
} );

// ─── REST page creation ───────────────────────────────────────────────────────

/**
 * Create a published WordPress page via the REST API and return its URL.
 *
 * @param {string} title    Page title
 * @param {string} content  Page body (may contain shortcodes)
 * @returns {Cypress.Chainable<string>} The published page URL
 */
Cypress.Commands.add( 'createPage', ( title, content ) => {
	return cy.request( {
		method: 'POST',
		url: '/wp-json/wp/v2/pages',
		auth: { user: Cypress.env( 'wpUser' ), pass: Cypress.env( 'wpPass' ) },
		body: { title, content, status: 'publish' },
	} ).then( ( resp ) => resp.body.link );
} );
