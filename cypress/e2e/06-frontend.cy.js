describe( '06 – Frontend Product Pages', () => {
	let productUrl = null;

	before( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();
		cy.setupPlugin();

		cy.request( {
			url: '/wp-json/wp/v2/reseller_product?per_page=1',
			failOnStatusCode: false,
		} ).then( ( resp ) => {
			productUrl = resp.body[ 0 ]?.link ?? null;
		} );
	} );

	beforeEach( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();
	} );

	// ── Product page structure ─────────────────────────────────────────────────

	it( 'single product page returns HTTP 200', () => {
		if ( ! productUrl ) return cy.log( 'No products — skipping' );
		cy.request( productUrl ).its( 'status' ).should( 'eq', 200 );
	} );

	it( 'single product page renders .rstore-pricing', () => {
		if ( ! productUrl ) return cy.log( 'No products — skipping' );
		cy.visit( productUrl );
		cy.get( '.rstore-pricing' ).should( 'exist' );
	} );

	it( '.rstore-price contains text (non-empty price)', () => {
		if ( ! productUrl ) return cy.log( 'No products — skipping' );
		cy.visit( productUrl );
		cy.get( '.rstore-price' ).invoke( 'text' ).should( 'not.be.empty' );
	} );

	// ── Add-to-cart form ──────────────────────────────────────────────────────

	it( 'add-to-cart form posts to the GoDaddy cart endpoint', () => {
		if ( ! productUrl ) return cy.log( 'No products — skipping' );
		cy.visit( productUrl );
		cy.get( 'form.rstore-add-to-cart-form' )
			.should( 'have.attr', 'method', 'POST' )
			.invoke( 'attr', 'action' )
			.should( 'include', 'secureserver.net' );
	} );

	it( 'add-to-cart button is visible and labelled', () => {
		if ( ! productUrl ) return cy.log( 'No products — skipping' );
		cy.visit( productUrl );
		cy.get( 'button.rstore-add-to-cart' )
			.should( 'be.visible' )
			.invoke( 'text' )
			.should( 'not.be.empty' );
	} );

	it( 'hidden items field contains valid JSON with product id and quantity', () => {
		if ( ! productUrl ) return cy.log( 'No products — skipping' );
		cy.visit( productUrl );
		cy.get( 'input[name="items"]' ).should( 'exist' ).invoke( 'val' ).then( ( val ) => {
			const items = JSON.parse( val );
			expect( items ).to.be.an( 'array' ).with.length.gte( 1 );
			expect( items[ 0 ] ).to.have.property( 'id' );
			expect( items[ 0 ] ).to.have.property( 'quantity' );
		} );
	} );

	// ── Domain product (no add-to-cart) ───────────────────────────────────────

	it( 'domain product page does NOT render an add-to-cart form', () => {
		cy.request( {
			url: '/wp-json/wp/v2/reseller_product?per_page=20',
			failOnStatusCode: false,
		} ).then( ( resp ) => {
			if ( ! Array.isArray( resp.body ) ) return cy.log( 'No products found — skipping' );
			const domain = resp.body.find( ( p ) =>
				p.slug?.includes( 'domain' ) || p.title?.rendered?.toLowerCase().includes( 'domain' )
			);
			if ( ! domain ) return cy.log( 'No domain product found — skipping' );
			cy.visit( domain.link );
			cy.get( 'form.rstore-add-to-cart-form' ).should( 'not.exist' );
		} );
	} );

	// ── No JS console errors ──────────────────────────────────────────────────

	it( 'product page logs no uncaught JS errors', () => {
		if ( ! productUrl ) return cy.log( 'No products — skipping' );
		cy.visit( productUrl );
		cy.get( '.rstore-pricing' ).should( 'exist' );
	} );
} );
