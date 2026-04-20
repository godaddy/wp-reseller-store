describe( '05 – Widgets', () => {
	before( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();
	} );

	it( 'widgets admin page loads without errors', () => {
		cy.visit( '/wp-admin/widgets.php' );
		cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
	} );

	const expectedWidgets = [
		{ title: 'Reseller Advanced Domain Search', formField: null },
		{ title: 'Reseller Domain Search',          formField: 'text_placeholder' },
		{ title: 'Reseller Cart Link',               formField: null },
		{ title: 'Reseller Shopper Login',           formField: null },
		{ title: 'Reseller Product',                 formField: null },
	];

	expectedWidgets.forEach( ( { title } ) => {
		it( `"${ title }" appears in the available widgets list`, () => {
			cy.visit( '/wp-admin/widgets.php' );
			cy.get( '#widget-list, .widgets-chooser' )
				.contains( title )
				.should( 'exist' );
		} );
	} );

	it( 'Domain Search widget expands to show configuration fields', () => {
		cy.visit( '/wp-admin/widgets.php' );

		// Locate the widget and open it
		cy.get( '#widget-list' )
			.contains( 'Reseller Domain Search' )
			.closest( '.widget' )
			.within( () => {
				cy.get( '.widget-title, h3, h4' ).first().click();
				// The form should appear with at least a title input
				cy.get( 'input[type="text"]' ).should( 'exist' );
			} );
	} );
} );
