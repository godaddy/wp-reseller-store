describe( '09 – Domain Search Cart', () => {
	before( () => {
		cy.stubExternalApis();
		cy.setupPlugin();
	} );

	beforeEach( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();
	} );

	// ── Helper ─────────────────────────────────────────────────────────────────

	function pageWithShortcode( title, shortcode ) {
		return cy.createPage( title, shortcode );
	}

	// ── Multi-domain add to cart ──────────────────────────────────────────────

	it( 'adds each selected domain via a sequential single-item request and navigates to the final nextStepUrl', () => {
		cy.intercept( 'GET', '**/api/v1/domains/**', { fixture: 'domain-search.json' } ).as( 'domainSearch' );

		cy.createPage( 'Domain Cart Confirmation', 'Your domains have been added to the cart.' ).then( ( confirmationUrl ) => {
			let cartCallCount = 0;
			const cartRequestBodies = [];

			cy.intercept( 'POST', '**/api/v1/cart/**', ( req ) => {
				cartCallCount += 1;
				cartRequestBodies.push( req.body );
				req.reply( {
					statusCode: 200,
					body: {
						cartCount:   cartCallCount,
						nextStepUrl: confirmationUrl,
					},
				} );
			} ).as( 'cartAdd' );

			pageWithShortcode( 'Test domain cart multi-select', '[rstore_domain_search]' ).then( ( url ) => {
				cy.visit( url );

				cy.get( '.rstore-domain-search-input' ).type( 'cypresstest.com' );
				cy.get( '.rstore-domain-search-button' ).click();
				cy.wait( '@domainSearch' );

				// Select both suggested domains; leave the exact match unselected.
				cy.get( '.rstore-domain-list .rstore-domain-buy-button.select' ).eq( 0 ).click();
				cy.get( '.rstore-domain-list .rstore-domain-buy-button.select' ).eq( 0 ).click();

				cy.get( '.rstore-domain-continue-button' ).click();

				cy.wait( '@cartAdd' );
				cy.wait( '@cartAdd' );

				cy.url().should( 'eq', confirmationUrl );

				cy.wrap( null ).should( () => {
					expect( cartRequestBodies ).to.have.length( 2 );

					const domains = cartRequestBodies.map( ( body ) => {
						const params = new URLSearchParams( body );
						const items  = JSON.parse( params.get( 'items' ) );

						expect( items ).to.have.length( 1 );
						expect( items[ 0 ] ).to.have.property( 'id', 'domain' );

						return items[ 0 ].domain;
					} );

					expect( domains ).to.deep.equal( [ 'cypresstest.net', 'cypresstest.org' ] );
				} );
			} );
		} );
	} );

	// ── Abort on first failure ────────────────────────────────────────────────

	it( 'stops on the first failed cart request, shows an error, and does not navigate away', () => {
		cy.intercept( 'GET', '**/api/v1/domains/**', { fixture: 'domain-search-single.json' } ).as( 'domainSearch' );
		cy.intercept( 'POST', '**/api/v1/cart/**', {
			statusCode: 500,
			body: { error: { message: 'Internal Server Error' } },
		} ).as( 'cartAdd' );

		pageWithShortcode( 'Test domain cart failure', '[rstore_domain_search]' ).then( ( url ) => {
			cy.visit( url );

			cy.get( '.rstore-domain-search-input' ).type( 'cypresstest2.com' );
			cy.get( '.rstore-domain-search-button' ).click();
			cy.wait( '@domainSearch' );

			// Nothing selected — exact match is used as the fallback single item.
			cy.get( '.rstore-domain-continue-button' ).click();
			cy.wait( '@cartAdd' );

			cy.get( '.rstore-error' ).should( 'contain.text', 'Internal Server Error' );
			cy.url().should( 'eq', url );
		} );
	} );
} );
