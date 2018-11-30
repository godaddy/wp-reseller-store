<?php
/**
 * GoDaddy Reseller Store Product Functions tests
 */

namespace Reseller_Store;

final class TestPostType extends TestCase {

	/**
	 * Tear Down.
	 */
	public function tearDown() {

		parent::tearDown();

		unset( $_POST['republish_product'] );
		unset( $_POST['post_type'] );
	}

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
			'view_mode_post_types',
			[
				'reseller_product' => 'reseller_product',
			]
		);

		$this->assertEquals( 0, count( $value ) );

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
			'imported',
			[
				$post->ID => 'wordpress-basic',
			]
		);

		$value = $post_type->reset_product_data( $post->ID );

		$this->assertFalse( $value );

	}

	/**
	 * @testdox Given a valid post republish_post should reset the product.
	 */
	public function test_republish_post() {

		rstore_update_option( 'pl_id', 1592 );
		$post_type = new Post_Type();
		new Taxonomy_Category;
		new Taxonomy_Tag;
		do_action( 'init' );

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );
		$post = Tests\Helper::create_product();

		rstore_update_option(
			'imported',
			[
				$post->ID => 'wordpress-basic',
			]
		);

		$_POST['republish_product'] = true;
		$_POST['post_type']         = 'reseller_product';

		$value = $post_type->republish_post( $post->ID );

		do_action( 'edit_form_top' );

		$this->assertNull( $value );
	}

	/**
	 * @testdox Given a republish_product parameter is not set republish_post should not reset the product.
	 */
	public function test_republish_post_without_parameter() {

		$post_type = new Post_Type();
		$user_id   = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );
		$post = Tests\Helper::create_product();

		$value = $post_type->republish_post( $post->ID );

		$this->assertNull( $value );

	}

	/**
	 * @testdox Given a post_type is not set republish_post should not reset the product.
	 */
	public function test_republish_post_wrong_post_type() {

		$post_type = new Post_Type();
		$user_id   = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );
		$post_id                    = $this->factory->post->create(
			array(
				'post_title' => 'test',
			)
		);
		$_POST['republish_product'] = 'reseller_product';

		$value = $post_type->republish_post( $post_id );

		$this->assertNull( $value );

	}

	/**
	 * @testdox Given a invalid post republish_post should not reset the product.
	 */
	public function test_republish_post_invalid() {

		$post_type = new Post_Type();
		$user_id   = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );
		$_POST['republish_product'] = true;
		$_POST['post_type']         = 'reseller_product';

		$post_id = wp_insert_post(
			[
				'post_title'  => 'test',
				'post_name'   => 'wordpress-hosting',
				'post_type'   => 'reseller_product',
				'post_status' => 'publish',
			]
		);

		$value = $post_type->republish_post( $post_id );
		do_action( 'edit_form_top' );

		$this->assertNull( $value );
		$this->expectOutputRegex( '/<div class="notice notice-error is-dismissible"><p>Error: Product id not found or invalid.<\/p><\/div>/' );

	}

	/**
	 * @testdox Given a unpublished post republish_post should not reset the product.
	 */
	public function test_republish_post_unpublished() {

		$post_type = new Post_Type();
		$user_id   = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		$post_id = wp_insert_post(
			[
				'post_title'  => 'hosting',
				'post_name'   => 'wordpress-hosting',
				'post_type'   => 'reseller_product',
				'post_status' => 'unpublish',
			]
		);

		$_POST['republish_product'] = true;

		$value = $post_type->republish_post( $post_id );

		$this->assertNull( $value );

	}

	/**
	 * @testdox Given auto save republish_post should not reset the product.
	 */
	public function test_republish_post_with_autosave() {

		define( 'DOING_AUTOSAVE', true );
		$post_type = new Post_Type();

		$value = $post_type->republish_post( 0 );

		$this->assertNull( $value );

	}

	/**
	 * @testdox Given filter rest_prepare_reseller_product it should return data.
	 */
	public function test_filter_rest_prepare_reseller_product() {

		rstore_update_option( 'pl_id', 1592 );
		$post = Tests\Helper::create_product( 'Test Product', 'test-product' );

		new Post_Type();

		$response = new \WP_REST_Response();

		$data = apply_filters( 'rest_prepare_reseller_product', $response, $post );

		$this->assertEquals( $data->data['plid'], 1592 );
		$this->assertEquals( $data->data['sku'], 'test-product' );

	}

	/**
	 * @testdox Given filter post_thumbnail_html it should return svg icon.
	 */
	public function test_filter_post_thumbnail_html_for_product() {

		rstore_update_option( 'pl_id', 1592 );
		$post = Tests\Helper::create_product( 'Test Product', 'test-product' );

		new Post_Type();

		$data = get_the_post_thumbnail( $post->ID, 'thumbnail' );

		$this->assertRegExp(
			'/<title>Additional Products<\/title>/',
			$data
		);

	}

	/**
	 * @testdox Given filter post_thumbnail_html for a post it should return html.
	 */
	public function test_filter_post_thumbnail_html_for_post() {

		rstore_update_option( 'pl_id', 1592 );

		$post_id = $this->factory->post->create(
			[
				'post_title' => 'test',
			]
		);

		new Post_Type();

		$data = get_the_post_thumbnail( $post_id, 'thumbnail' );

		$this->assertEquals( '', $data );

	}
}

