// ─── Login ────────────────────────────────────────────────────────────────────

/**
 * Log in to WordPress admin using cy.session() so the cookie is cached
 * and reused across specs without re-visiting the login page every time.
 */
Cypress.Commands.add( 'loginAsAdmin', () => {
	// cy.request() shares Cypress's cookie jar with cy.visit().
	// WordPress's login checks for wordpress_test_cookie in the Cookie header.
	// Set it via cy.setCookie() so cy.request() automatically includes it.
	cy.setCookie( 'wordpress_test_cookie', 'WP Cookie check' );

	cy.request( {
		method: 'POST',
		url:    '/wp-login.php',
		form:   true,
		body: {
			log:         Cypress.env( 'wpUser' ),
			pwd:         Cypress.env( 'wpPass' ),
			'wp-submit': 'Log In',
			redirect_to: '/wp-admin/',
			testcookie:  '1',
		},
	} ).then( ( resp ) => {
		// After a successful login WordPress redirects (302) to /wp-admin/.
		// cy.request() follows redirects by default so the final status is 200.
		expect( resp.status ).to.eq( 200 );
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

// ─── REST API nonce ───────────────────────────────────────────────────────────

/**
 * Visit WP admin and return the REST API nonce from wpApiSettings.
 * post-new.php reliably enqueues wp-api.js regardless of active theme.
 * Required for write operations (POST/PUT/DELETE) via the WP REST API
 * when using cookie-based authentication.
 */
Cypress.Commands.add( 'getRestNonce', () => {
	return cy.visit( '/wp-admin/post-new.php' ).then( () =>
		cy.window().its( 'wpApiSettings.nonce' )
	);
} );

// ─── Theme switching ──────────────────────────────────────────────────────────

/**
 * Activate a WordPress theme by slug via the REST API.
 *
 * @param {string} slug Theme slug, e.g. 'twentytwentyfour' or 'storefront'
 */
Cypress.Commands.add( 'activateTheme', ( slug ) => {
	cy.getRestNonce().then( ( nonce ) => {
		cy.request( {
			method: 'POST',
			url: '/wp-json/wp/v2/settings',
			headers: { 'X-WP-Nonce': nonce },
			body: { stylesheet: slug },
		} ).its( 'status' ).should( 'eq', 200 );
	} );
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
	return cy.getRestNonce().then( ( nonce ) => {
		return cy.request( {
			method: 'POST',
			url: '/wp-json/wp/v2/pages',
			headers: { 'X-WP-Nonce': nonce },
			body: { title, content, status: 'publish' },
		} ).then( ( resp ) => resp.body.link );
	} );
} );
