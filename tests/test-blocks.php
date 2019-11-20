<?php
/**
 * GoDaddy Reseller Gutenberg Blocks tests
 */

namespace Reseller_Store;

final class TestBlocks extends TestCase {

	/**
	 * @testdox Test block class exist.
	 */
	function test_basics() {

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Blocks' ),
			'Class \Blocks is not found'
		);

	}

	/**
	 * @testdox Given a valid instance and function exists the scripts should enqueue
	 */
	function test_block_instance() {

		new Blocks();

		do_action( 'enqueue_block_editor_assets' );

		if ( function_exists( 'register_block_type' ) ) {

			$this->assertTrue( wp_script_is( 'reseller-store-blocks-js' ), 'done' );
			$this->assertTrue( wp_style_is( 'reseller-store-blocks-css' ), 'done' );

		} else {

			$this->assertFalse( wp_script_is( 'reseller-store-blocks-js' ), 'done' );
			$this->assertFalse( wp_style_is( 'reseller-store-blocks-css' ), 'done' );

		}

	}

	/**
	 * @testdox Given a valid instance the enqueue function should load scrips
	 */
	function test_block_enqueue_function() {

		$blocks = new Blocks();

		$blocks->enqueue_block_editor_assets();

		$this->assertTrue( wp_script_is( 'reseller-store-blocks-js' ), 'done' );
		$this->assertTrue( wp_style_is( 'reseller-store-blocks-css' ), 'done' );

	}

	/**
	 * @testdox Given block categories it should add Reseller Store Modules
	 */
	function test_block_categories() {

		$blocks = new Blocks();

		$categories = array(
			array(
				'slug'  => 'category1',
				'title' => 'Category 1',
			),
		);

		$new_categories = $blocks->block_categories( $categories );

		$this->assertEquals( $new_categories[1]['slug'], 'reseller-store' );
		$this->assertEquals( $new_categories[1]['title'], 'Reseller Store Modules' );

	}

	/**
	 * @testdox Given a product block it should render
	 */
	function test_product_block() {

		$post = Tests\Helper::create_product( 'Test Product' );

		$blocks = new Blocks();

		$instance = array(
			'post_id' => $post->ID,
		);

		$this->assertRegExp(
			'/Test Product/',
			$blocks->product( $instance )
		);

	}

	/**
	 * @testdox Given a domain search block it should render
	 */
	function test_domain_search_block() {

		$blocks = new Blocks();

		rstore_update_option( 'pl_id', 12345 );

		$instance = array(
			'title'            => 'title',
			'text_placeholder' => 'find your domain',
		);

		$this->assertRegExp(
			'/<form role="search" method="get" class="search-form" action="https:\/\/www.secureserver.net\/products\/domain-registration\/find\/\?plid=12345">/',
			$blocks->domain_search( $instance )
		);

	}

	/**
	 * @testdox Given a domain advanced search block it should render
	 */
	function test_domain_search_advanced_block() {

		$blocks = new Blocks();

		rstore_update_option( 'pl_id', 12345 );

		$instance = array(
			'title'            => 'title',
			'text_placeholder' => 'find your domain',
			'search_type'      => 'advanced',
		);

		$this->assertRegExp(
			'/<div class=\"rstore-domain-search\" data-plid=\"12345\" data-title=\"title\" data-page_size=\"5\" data-text_placeholder=\"find your domain\" data-text_search=\"Search\" data-text_available=\"Congrats, {domain_name} is available!\" data-text_not_available=\"Sorry, {domain_name} is taken.\" data-text_cart=\"Continue to cart\" data-text_select=\"Select\" data-text_selected=\"Selected\">Domain Search<\/div>/',
			$blocks->domain_search( $instance )
		);

	}

	/**
	 * @testdox Given a domain transfer search block it should render
	 */
	function test_domain_search_transfer_block() {

		$blocks = new Blocks();

		rstore_update_option( 'pl_id', 12345 );

		$instance = array(
			'title'            => 'title',
			'text_placeholder' => 'transfer your domain',
			'search_type'      => 'transfer',
		);

		$this->assertRegExp(
			'/<form role=\"search\" method=\"get\" class=\"search-form\" action=\"https:\/\/www.secureserver.net\/products\/domain-transfer\/\?plid=12345\">/',
			$blocks->domain_search( $instance )
		);

	}

}
