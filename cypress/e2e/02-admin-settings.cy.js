const SETTINGS_URL = '/wp-admin/edit.php?post_type=reseller_product&page=rstore_settings';

describe( '02 – Admin Settings', () => {
	before( () => {
		cy.stubExternalApis();
		cy.setupPlugin();
	} );

	beforeEach( () => {
		cy.stubExternalApis();
		cy.loginAsAdmin();
	} );

	// ── Tab rendering ──────────────────────────────────────────────────────────

	const tabs = [
		{
			slug:   'setup_options',
			label:  'Setup',
			fields: [ 'pl_id', 'sync_ttl' ],
		},
		{
			slug:   'product_options',
			label:  'Product Settings',
			fields: [ 'product_button_label', 'product_image_size', 'product_layout_type' ],
		},
		{
			slug:   'domain_options',
			label:  'Domain Search Settings',
			fields: [ 'domain_title', 'domain_text_placeholder', 'domain_text_search' ],
		},
		{
			slug:   'localization_options',
			label:  'Localization',
			fields: [ 'api_currency', 'api_market' ],
		},
	];

	tabs.forEach( ( { slug, label, fields } ) => {
		it( `"${ label }" tab renders all expected fields`, () => {
			cy.visit( `${ SETTINGS_URL }&tab=${ slug }` );
			cy.get( '.nav-tab-active' ).should( 'contain.text', label );
			cy.get( 'body' ).should( 'not.contain.text', 'Fatal error' );
			fields.forEach( ( id ) => cy.get( `#${ id }` ).should( 'exist' ) );
		} );
	} );

	// ── Save ───────────────────────────────────────────────────────────────────

	it( 'saves product_button_label without errors', () => {
		cy.visit( `${ SETTINGS_URL }&tab=product_options` );
		cy.get( '#product_button_label' ).clear().type( 'Buy Now' );
		cy.get( 'button[type="submit"]' ).click();
		cy.get( '#rstore-options-save-error', { timeout: 8000 } ).should( 'be.empty' );
	} );

	it( 'restores product_button_label default (empty = no override)', () => {
		cy.visit( `${ SETTINGS_URL }&tab=product_options` );
		cy.get( '#product_button_label' ).clear();
		cy.get( 'button[type="submit"]' ).click();
		cy.get( '#rstore-options-save-error', { timeout: 8000 } ).should( 'be.empty' );
	} );

	// ── Branding info ─────────────────────────────────────────────────────────

	it( 'branding info table is rendered on the setup tab', () => {
		cy.visit( `${ SETTINGS_URL }&tab=setup_options` );
		cy.get( '#rstore-branding-info' ).should( 'exist' );
	} );

	// ── Import button ─────────────────────────────────────────────────────────

	it( 'Import new products button is visible on the setup tab', () => {
		cy.visit( `${ SETTINGS_URL }&tab=setup_options` );
		cy.get( '#rstore-product-import button[type="submit"]' )
			.should( 'be.visible' )
			.and( 'contain.text', 'Import new products' );
	} );
} );
