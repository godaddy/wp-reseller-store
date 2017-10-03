<?php
/**
 * GoDaddy Reseller Store Product Functions tests
 */

namespace Reseller_Store;

final class TestPostType extends TestCase {

	/**
	 * @testdox Given edit_reseller_product filter it should return 50.
	 */
	public function test_edit_reseller_product_per_page_filter() {

		new Post_Type();
		$value = apply_filters( 'edit_reseller_product_per_page', '' );

		$this->assertEquals( 50, $value );

	}

	/**
	 * @testdox Given an array the sortable_columns filter should add price to the array.
	 */
	public function test_manage_edit_reseller_product_sortable_columns_filter() {

		new Post_Type();
		// @codingStandardsIgnoreStart
		$columns = apply_filters( 'manage_edit-reseller_product_sortable_columns', [] );
		// @codingStandardsIgnoreEnd

		$this->assertEquals( 'price', $columns['price'] );

	}

	/**
	 * @testdox Given reseller_store the view_mode_post_types filter should return true.
	 */
	public function test_view_mode_post_types_filter() {

		new Post_Type();
		$value = apply_filters(
			'view_mode_post_types', [
				'reseller_product' => 'reseller_product',
			]
		);

		$this->assertEquals( 0, count( $value ) );

	}

	/**
	 * @testdox Given do_action add_meta_boxes should add_meta_box.
	 */
	public function test_add_meta_boxes() {

		new Post_Type();
		do_action( 'add_meta_boxes' );
		$this->AssertTrue( true );

	}

	/**
	 * @testdox Given render_checkbox function should render a checkbox.
	 */
	public function test_render_checkbox() {

		$post_type = new Post_Type();
		$post_type->render_checkbox();

		$this->expectOutputRegex( '/<input type="checkbox" id="republish_product" name="republish_product" >/' );
		$this->expectOutputRegex( '/<label for="restore_product">Republish your product data with the latest version. This will overwrite any changes you have made.<\/label>/' );

	}

	/**
	 * @testdox Given a valid post_id reset_product_data should reset the product.
	 */
	public function test_reset_product_data() {

		$post_type = new Post_Type();
		new Taxonomy_Category;
		new Taxonomy_Tag;
		do_action( 'init' );

		rstore_update_option( 'pl_id', 1592 );

		$post = Tests\Helper::create_product();

		rstore_update_option(
			'imported', [
				$post->ID => 'wordpress-basic',
			]
		);

		$value = $post_type->reset_product_data( $post->ID );

		$this->assertFalse( $value ); // option did not change.

	}
}
