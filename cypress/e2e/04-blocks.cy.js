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

		// Wait for the inspector to show the product selector — this signals the products
		// REST call has resolved, the useEffect has fired, and post_id has been persisted
		// on the block attributes before we save.
		cy.contains( '.components-panel, .block-editor-block-inspector', 'Select Product', { timeout: 10000 } )
			.should( 'exist' );

		// Save as draft without touching the product selector — simpler and version-agnostic
		cy.get( 'button.editor-post-save-draft', { timeout: 5000 } ).click();
		cy.get( '.editor-post-saved-state', { timeout: 8000 } ).should( 'contain.text', 'Saved' );

		// Publish via REST API using the nonce already present in the editor window,
		// then visit the live URL — avoids the fragile multi-step publish UI flow.
		cy.url().then( ( editorUrl ) => {
			const postId = new URL( editorUrl ).searchParams.get( 'post' );

			cy.window().its( 'wpApiSettings.nonce' ).then( ( nonce ) => {
				cy.request( {
					method: 'POST',
					url: `/wp-json/wp/v2/posts/${ postId }`,
					headers: { 'X-WP-Nonce': nonce },
					body: { status: 'publish' },
				} ).then( ( resp ) => {
					cy.visit( resp.body.link );
				} );
			} );
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
