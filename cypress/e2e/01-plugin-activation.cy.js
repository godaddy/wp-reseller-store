describe( '01 – Plugin Activation', () => {
	before( () => cy.loginAsAdmin() );

	it( 'plugin is listed as active on the plugins screen', () => {
		cy.visit( '/wp-admin/plugins.php' );
		cy.get( 'tr[data-slug="reseller-store"]' ).should( 'have.class', 'active' );
	} );

	it( 'reseller_product post type appears in the admin menu', () => {
		cy.visit( '/wp-admin/edit.php?post_type=reseller_product' );
		cy.get( 'h1' ).should( 'contain.text', 'Reseller' );
		cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
	} );

	it( 'Setup and Settings submenus are present', () => {
		cy.visit( '/wp-admin/edit.php?post_type=reseller_product' );
		cy.get( '#adminmenu' ).contains( 'Setup' ).should( 'exist' );
		cy.get( '#adminmenu' ).contains( 'Settings' ).should( 'exist' );
	} );

	it( 'Setup page loads the reseller-store setup section', () => {
		cy.visit( '/wp-admin/admin.php?page=reseller-store-setup' );
		cy.get( '.rstore-setup' ).should( 'exist' );
		cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
	} );

	it( 'admin dashboard has no PHP warnings or notices', () => {
		cy.visit( '/wp-admin/index.php' );
		cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
		cy.get( 'body' ).should( 'not.contain.text', 'Uncaught Error' );
	} );

	it( 'plugin assets are enqueued on the products list page', () => {
		cy.visit( '/wp-admin/edit.php?post_type=reseller_product' );
		cy.get( 'link[href*="reseller-store"]' ).should( 'exist' );
	} );
} );
