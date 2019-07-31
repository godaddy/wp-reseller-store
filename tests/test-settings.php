<?php
/**
 * GoDaddy Reseller Store test settings class
 */

namespace Reseller_Store;

final class TestClassSettings extends TestCase {

	/**
	 * Tear Down.
	 */
	public function tearDown() {

		parent::tearDown();

		$_SERVER['REQUEST_URI'] = '';
		unset( $_REQUEST );

	}

	/**
	 * @testdox Test that Display class exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Settings' ) );

	}

	/**
	 * @testdox Given the action wp_enqueue_scripts the styles and scripts should render
	 */
	public function test_admin_enqueue_scripts() {

		set_current_screen( 'edit.php?post_type=reseller_product' );
		$_SERVER['REQUEST_URI'] = 'edit.php?post_type=reseller_product';

		new Settings();
		do_action( 'admin_enqueue_scripts' );

		$this->assertTrue( wp_style_is( 'reseller-store-admin-css' ), 'done' );
		$this->assertTrue( wp_script_is( 'reseller-store-settings-js' ), 'done' );

	}

	/**
	 * @testdox Given the the correct options the filters should be created
	 */
	public function test_basic_filters() {

		$options = [
			'product_layout_type',
			'product_image_size',
			'product_button_label',
			'product_text_cart',
			'product_text_more',
			'domain_title',
			'domain_text_placeholder',
			'domain_text_search',
			'domain_transfer_title',
			'domain_transfer_text_placeholder',
			'domain_transfer_text_search',
			'domain_page_size',
			'domain_modal',
			'sync_ttl',
		];

		foreach ( $options as $option ) {
			rstore_update_option( $option, true );
		}

		new Settings();

		foreach ( $options as $option ) {

			$this->assertTrue( apply_filters( 'rstore_' . $option, false ) );
		}

	}

	/**
	 * @testdox Given the the correct boolean options the filters should be created
	 */
	public function test_boolean_filters() {

		$options = [
			'product_show_title',
			'product_show_content',
			'product_show_price',
			'product_redirect',
		];

		foreach ( $options as $option ) {
			rstore_update_option( $option, true );
		}

		new Settings();

		foreach ( $options as $option ) {

			$this->assertFalse( apply_filters( 'rstore_' . $option, true ) );
		}

	}

	/**
	 * @testdox Given product_full_content_height options the filters should return 0
	 */
	public function test_product_full_content_filters() {

		rstore_update_option( 'product_content_height', true );
		rstore_update_option( 'product_full_content_height', true );

		new Settings();

		$this->assertEquals( 0, apply_filters( 'rstore_product_content_height', 100 ) );

	}

	/**
	 * @testdox Given product_content height options the filters should return the height
	 */
	public function test_product_content_height_filters() {

		rstore_update_option( 'product_content_height', 50 );

		new Settings();

		$this->assertEquals( 50, apply_filters( 'rstore_product_content_height', 100 ) );

	}

	/**
	 * @testdox Given product_isc the filters should return the isc in the args
	 */
	public function test_product_isc_filters() {

		rstore_update_option( 'product_isc', 'promotion' );

		new Settings();

		$args = apply_filters( 'rstore_api_query_args', [], 'cart_api' );

		$this->assertEquals( 'promotion', $args['isc'] );

	}

	/**
	 * @testdox Given api_market the filters should return the isc in the args
	 */
	public function test_api_market_filters() {

		rstore_update_option( 'product_isc', 'promotion' );

		new Settings();

		$args = apply_filters( 'rstore_api_query_args', [], 'cart_api' );

		$this->assertEquals( 'promotion', $args['isc'] );

	}

	/**
	 * @testdox Given api_market the filters should return the isc in the args
	 */
	public function test_api_currency_filters() {

		rstore_update_option( 'product_isc', 'promotion' );

		new Settings();

		$args = apply_filters( 'rstore_api_query_args', [], 'cart_api' );

		$this->assertEquals( 'promotion', $args['isc'] );

	}

}
