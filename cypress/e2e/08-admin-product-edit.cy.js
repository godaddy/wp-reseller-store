describe( '08 – Admin Product Edit', () => {
	let productPostId = null;

	before( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();
		cy.setupPlugin();

		cy.request( {
			url: '/wp-json/wp/v2/reseller_product?per_page=1',
			failOnStatusCode: false,
		} ).then( ( resp ) => {
			productPostId = resp.body[ 0 ]?.id ?? null;
		} );
	} );

	beforeEach( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();
	} );

	it( 'products list page loads without fatal errors', () => {
		cy.visit( '/wp-admin/edit.php?post_type=reseller_product' );
		cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
		cy.get( 'body' ).should( 'not.contain.text', 'TypeError' );
	} );

	it( 'edit screen loads without fatal errors', () => {
		if ( !productPostId ) return cy.log( 'No products imported — skipping' );

		cy.visit( `/wp-admin/post.php?post=${productPostId}&action=edit` );

		cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
		cy.get( 'body' ).should( 'not.contain.text', 'TypeError' );
		cy.get( 'body' ).should( 'not.contain.text', 'critical error' );
	} );

	it( 'edit screen heading shows "Edit:" prefix with product title', () => {
		if ( !productPostId ) return cy.log( 'No products imported — skipping' );

		cy.visit( `/wp-admin/post.php?post=${productPostId}&action=edit` );

		// post_screen_edit_heading filter sets the h1 to "Edit: <product title>"
		cy.get( '#wpbody h1', { timeout: 8000 } )
			.invoke( 'text' )
			.should( 'match', /Edit:/i );
	} );

	it( 'edit screen shows ButterBean product options meta box', () => {
		if ( !productPostId ) return cy.log( 'No products imported — skipping' );

		cy.visit( `/wp-admin/post.php?post=${productPostId}&action=edit` );

		cy.get( '.butterbean-ui, #product_options, [id*="butterbean"]', { timeout: 8000 } )
			.should( 'exist' );
	} );
} );
