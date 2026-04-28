describe( '04 – Gutenberg Blocks', () => {
	beforeEach( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();
		cy.visit( '/wp-admin/post-new.php' );

		// Wait for the editor to start rendering (modal may appear before writing flow)
		cy.get(
			'.components-modal__screen-overlay, .block-editor-writing-flow, .editor-styles-wrapper',
			{ timeout: 20000 }
		).should( 'exist' );

		// Dismiss welcome/tour dialog if present
		cy.get( 'body' ).then( ( $body ) => {
			if ( $body.find( '.components-modal__screen-overlay' ).length ) {
				cy.get( '.components-modal__screen-overlay' ).type( '{esc}' );
			}
		} );

		// Wait for the block editor canvas to be ready
		cy.get( '.block-editor-writing-flow, .editor-styles-wrapper', { timeout: 10000 } )
			.should( 'exist' );
	} );

	it( 'block editor loads without fatal errors', () => {
		cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
		cy.get( '.block-editor-writing-flow' ).should( 'exist' );
	} );

	it( 'block inserter finds Reseller Store blocks', () => {
		// Open the inserter
		cy.get( 'button[aria-label*="Block Inserter" i], button[aria-label*="Toggle block inserter" i]', { timeout: 10000 } )
			.first().click();

		// Search for Reseller
		cy.get( 'input[placeholder*="Search"]', { timeout: 8000 } )
			.first()
			.type( 'Domain Search' );

		cy.contains( '.block-editor-block-types-list__item', 'Domain Search', { timeout: 8000 } )
			.should( 'have.length.gte', 1 );
	} );

	it( 'inserts Domain Search block and renders block wrapper', () => {
		cy.get( 'button[aria-label*="Block Inserter" i], button[aria-label*="Toggle block inserter" i]', { timeout: 10000 } )
			.first().click();

		cy.get( 'input[placeholder*="Search"]', { timeout: 8000 } )
			.first()
			.type( 'Domain Search' );

		cy.contains( '.block-editor-block-types-list__item', 'Domain Search', { timeout: 8000 } )
			.first().click();

		cy.get( '[data-type="reseller-store/domain-search"]', { timeout: 8000 } )
			.should( 'exist' );
	} );

	it( 'Product block auto-selects first product and renders on save without manual selection', () => {
		// Insert the Product block
		cy.get( 'button[aria-label*="Block Inserter" i], button[aria-label*="Toggle block inserter" i]', { timeout: 10000 } )
			.first().click();

		cy.get( 'input[placeholder*="Search"]', { timeout: 8000 } )
			.first()
			.type( 'Product' );

		cy.contains( '.block-editor-block-types-list__item-title, .block-editor-block-types-list__item span', /^Product$/, { timeout: 8000 } )
			.closest( '.block-editor-block-types-list__item' )
			.first().click();

		cy.get( '[data-type="reseller-store/product"]', { timeout: 8000 } )
			.should( 'exist' );

		// Save the post without touching the product selector
		cy.get( 'button.editor-post-publish-panel__toggle, button.editor-post-save-draft' ).first().click();
		cy.get( 'button.editor-post-publish-button, button.editor-post-publish-panel__publish-button', { timeout: 5000 } )
			.first().click();

		// Grab the published post URL and visit it
		cy.get( '.post-publish-panel__postpublish-post-address a, a.components-button[href*="/?p="]', { timeout: 10000 } )
			.first()
			.invoke( 'attr', 'href' )
			.then( ( url ) => {
				cy.visit( url );
			} );

		// The product block should render product content — not the error message
		cy.get( 'body' ).should( 'not.contain.text', 'Post id is not valid.' );
		cy.get( '.rstore-product, .wp-block-reseller-store-product', { timeout: 8000 } )
			.should( 'exist' );
	} );

	it( 'inserts Product block and shows block inspector', () => {
		cy.get( 'button[aria-label*="Block Inserter" i], button[aria-label*="Toggle block inserter" i]', { timeout: 10000 } )
			.first().click();

		cy.get( 'input[placeholder*="Search"]', { timeout: 8000 } )
			.first()
			.type( 'Product' );

		// Use exact-match regex to avoid matching "WooCommerce Recent Products" etc.
		cy.contains( '.block-editor-block-types-list__item-title, .block-editor-block-types-list__item span', /^Product$/, { timeout: 8000 } )
			.closest( '.block-editor-block-types-list__item' )
			.first().click();

		cy.get( '[data-type="reseller-store/product"]', { timeout: 8000 } )
			.should( 'exist' );

		cy.get( '.block-editor-block-inspector, .components-panel', { timeout: 5000 } )
			.should( 'exist' );
	} );
} );
