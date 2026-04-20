describe( '03 – Shortcodes', () => {
	before( () => {
		cy.stubExternalApis();
		cy.setupPlugin();
	} );

	// ── Helper ─────────────────────────────────────────────────────────────────

	function pageWithShortcode( title, shortcode ) {
		return cy.createPage( title, shortcode );
	}

	// ── Domain simple ─────────────────────────────────────────────────────────

	it( '[rstore_domain] renders a domain search input', () => {
		pageWithShortcode( 'Test rstore_domain', '[rstore_domain]' ).then( ( url ) => {
			cy.visit( url );
			cy.get( '.rstore-domain-search-form, input[name="domainToCheck"], .rstore-domain' )
				.should( 'exist' );
		} );
	} );

	// ── Domain transfer ───────────────────────────────────────────────────────

	it( '[rstore_domain_transfer] renders a transfer form', () => {
		pageWithShortcode( 'Test rstore_domain_transfer', '[rstore_domain_transfer]' ).then( ( url ) => {
			cy.visit( url );
			cy.get( '.rstore-domain-transfer-form, .rstore-domain-transfer, input[type="text"]' )
				.should( 'exist' );
		} );
	} );

	// ── Domain search (advanced React widget) ─────────────────────────────────

	it( '[rstore_domain_search] renders the React domain-search container', () => {
		pageWithShortcode( 'Test rstore_domain_search', '[rstore_domain_search]' ).then( ( url ) => {
			cy.visit( url );
			cy.get( '.rstore-domain-search' ).should( 'exist' );
		} );
	} );

	it( '[rstore_domain_search] injects a data-plid attribute', () => {
		pageWithShortcode( 'Test domain-search plid', '[rstore_domain_search]' ).then( ( url ) => {
			cy.visit( url );
			cy.get( '.rstore-domain-search' ).should( 'have.attr', 'data-plid' );
		} );
	} );

	// ── Cart button ───────────────────────────────────────────────────────────

	it( '[rstore_cart_button] renders a cart link', () => {
		pageWithShortcode( 'Test rstore_cart_button', '[rstore_cart_button]' ).then( ( url ) => {
			cy.visit( url );
			cy.get( '.rstore-view-cart a, .rstore-cart-button a' )
				.should( 'exist' )
				.and( 'have.attr', 'href' );
		} );
	} );

	// ── Login ─────────────────────────────────────────────────────────────────

	it( '[rstore_login] renders the login/logout status container', () => {
		pageWithShortcode( 'Test rstore_login', '[rstore_login]' ).then( ( url ) => {
			cy.visit( url );
			cy.get( '.rstore-login, .rstore-login-status, .rstore-login-btn' )
				.should( 'exist' );
		} );
	} );

	// ── Product (requires imported product) ───────────────────────────────────

	it( '[rstore_product] renders pricing and add-to-cart when a product exists', () => {
		cy.request( {
			url: '/wp-json/wp/v2/reseller_product?per_page=1',
			auth: { user: Cypress.env( 'wpUser' ), pass: Cypress.env( 'wpPass' ) },
			failOnStatusCode: false,
		} ).then( ( resp ) => {
			const post = resp.body[ 0 ];
			if ( ! post ) {
				cy.log( 'No reseller products imported — skipping product shortcode test' );
				return;
			}

			pageWithShortcode(
				'Test rstore_product',
				`[rstore_product post_id="${ post.id }"]`
			).then( ( url ) => {
				cy.visit( url );
				cy.get( '.rstore-pricing' ).should( 'exist' );
				cy.get( '.rstore-price' ).should( 'not.be.empty' );
				cy.get( 'button.rstore-add-to-cart' ).should( 'exist' );
			} );
		} );
	} );
} );
