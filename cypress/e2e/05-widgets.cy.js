describe( '05 – Widgets', () => {
	before( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();

		// Block themes (e.g. Twenty Twenty-Four) have no widget areas.
		// Switch to a classic theme and enable Classic Widgets plugin.
		cy.exec(
			'npx wp-env run cli -- wp theme install storefront --activate',
			{ failOnNonZeroExit: false }
		);
		cy.exec(
			'npx wp-env run cli -- wp plugin install classic-widgets --activate',
			{ failOnNonZeroExit: false }
		);
	} );

	beforeEach( () => cy.loginAsAdmin() );

	it( 'widgets admin page loads without errors', () => {
		cy.visit( '/wp-admin/widgets.php', { failOnStatusCode: false } );
		cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
		cy.get( '#wpwrap' ).should( 'exist' );
	} );

	const expectedWidgets = [
		'Reseller Advanced Domain Search',
		'Reseller Domain Search',
		'Reseller Cart Link',
		'Reseller Shopper Login',
		'Reseller Product',
	];

	expectedWidgets.forEach( ( title ) => {
		it( `"${ title }" appears in the available widgets list`, () => {
			cy.visit( '/wp-admin/widgets.php', { failOnStatusCode: false } );
			cy.get( 'body' ).contains( title ).should( 'exist' );
		} );
	} );

	it( 'Domain Search widget expands to show configuration fields', () => {
		cy.visit( '/wp-admin/widgets.php', { failOnStatusCode: false } );

		cy.contains( 'Reseller Domain Search' )
			.closest( '.widget' )
			.within( () => {
				cy.get( '.widget-title, h3, h4' ).first().click();
				cy.get( 'input[type="text"]' ).should( 'exist' );
			} );
	} );

	after( () => {
		// Restore the default theme and deactivate Classic Widgets
		cy.exec(
			'npx wp-env run cli -- wp theme activate twentytwentyfour',
			{ failOnNonZeroExit: false }
		);
		cy.exec(
			'npx wp-env run cli -- wp plugin deactivate classic-widgets',
			{ failOnNonZeroExit: false }
		);
	} );
} );
