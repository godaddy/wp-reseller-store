describe( '04 – Gutenberg Blocks', () => {
	before( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();
	} );

	beforeEach( () => {
		cy.visit( '/wp-admin/post-new.php' );

		// Dismiss welcome/tour dialog if present
		cy.get( 'body' ).then( ( $body ) => {
			if ( $body.find( '.components-modal__screen-overlay' ).length ) {
				cy.get( '.components-modal__screen-overlay' ).type( '{esc}' );
			}
		} );

		// Wait for the block editor canvas to be ready
		cy.get( '.block-editor-writing-flow, .editor-styles-wrapper', { timeout: 15000 } )
			.should( 'exist' );
	} );

	it( 'block editor loads without fatal errors', () => {
		cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
		cy.get( '.block-editor-writing-flow' ).should( 'exist' );
	} );

	it( 'block inserter finds Reseller Store blocks', () => {
		// Open the inserter
		cy.get( 'button[aria-label*="Toggle block inserter"], .editor-document-tools__inserter-toggle' )
			.first().click();

		// Search for Reseller
		cy.get( '.block-editor-inserter__search input, input[placeholder*="Search"]', { timeout: 8000 } )
			.type( 'Domain Search' );

		cy.get( '.block-editor-block-types-list__item, .block-editor-inserter__block-list button' )
			.should( 'have.length.gte', 1 );
	} );

	it( 'inserts Domain Search block and renders block wrapper', () => {
		cy.get( 'button[aria-label*="Toggle block inserter"], .editor-document-tools__inserter-toggle' )
			.first().click();

		cy.get( '.block-editor-inserter__search input, input[placeholder*="Search"]', { timeout: 8000 } )
			.type( 'Domain Search' );

		cy.get( '.block-editor-block-types-list__item' )
			.first().click();

		cy.get( '[data-type="reseller-store/domain-search"]', { timeout: 8000 } )
			.should( 'exist' );
	} );

	it( 'inserts Product block and shows block inspector', () => {
		cy.get( 'button[aria-label*="Toggle block inserter"], .editor-document-tools__inserter-toggle' )
			.first().click();

		cy.get( '.block-editor-inserter__search input, input[placeholder*="Search"]', { timeout: 8000 } )
			.type( 'Product' );

		cy.contains( '.block-editor-block-types-list__item', 'Product' )
			.first().click();

		cy.get( '[data-type="reseller-store/product"]', { timeout: 8000 } )
			.should( 'exist' );

		cy.get( '.block-editor-block-inspector, .components-panel', { timeout: 5000 } )
			.should( 'exist' );
	} );
} );
