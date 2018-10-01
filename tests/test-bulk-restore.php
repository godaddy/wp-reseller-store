<?php
/**
 * GoDaddy Reseller Store Bulk Restore class tests
 */

namespace Reseller_Store;

final class TestBulkRestore extends TestCase {

	/**
	 * @testdox Test that Bulk Restore class exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Bulk_Restore' ) );

	}

	/**
	 * @testdox Test that Bulk Actions filter adds a bulk action.
	 */
	public function test_bulk_actions_filter() {

		new Bulk_Restore();
		$action_name = 'bulk_actions-edit-' . Post_Type::SLUG;
		$actions     = apply_filters( $action_name, [] );
		$this->assertEquals( 'Restore Product Data', $actions['restore_product_data'] );

	}

	/**
	 * @testdox Given valid product, the bulk_action_handler should restore from the api.
	 */
	public function test_bulk_action_handler() {

		rstore_update_option( 'pl_id', 1592 );
		new Post_Type();
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

		$post_ids = [ $post->ID ];

		$bulk_restore = new Bulk_Restore();

		$redirect_to = $bulk_restore->bulk_action_handler( '', 'restore_product_data', $post_ids );

		$errors = rstore_get_option( 'errors', [] );

		$this->assertEquals( 0, count( $errors ) );
		$this->assertEquals( '?bulk_restore_posts=1', $redirect_to );

	}

	/**
	 * @testdox Given invalid action, the bulk_action_handler should return.
	 */
	public function test_bulk_action_handler_invalid_action() {

		$bulk_restore = new Bulk_Restore();

		$redirect_to = $bulk_restore->bulk_action_handler( '', 'invalid', [] );

		$error = rstore_get_option( 'errors', [] );

		$this->assertEquals( 0, count( $error ) );
		$this->assertEmpty( $redirect_to );

	}

	/**
	 * @testdox Given an api error, the bulk_action_handler should set an error.
	 */
	public function test_bulk_action_handler_api_error() {

		$post = Tests\Helper::create_product();

		$post_ids = [ $post->ID ];

		$bulk_restore = new Bulk_Restore();

		$redirect_to = $bulk_restore->bulk_action_handler( '', 'restore_product_data', $post_ids );

		$errors = rstore_get_option( 'errors', [] );

		$this->assertEquals( 1, count( $errors ) );
		$this->assertEquals( 'Bad Request', $errors[0]->get_error_message() );
		$this->assertEmpty( $redirect_to );

	}


	/**
	 * @testdox Given missing product id meta, the bulk_action_handler should set an error.
	 */
	public function test_bulk_action_handler_missing_product_id() {

		rstore_update_option( 'pl_id', 1592 );

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		$post_id = wp_insert_post(
			[
				'post_title'   => 'WordPress',
				'post_name'    => 'wordpress-hosting',
				'post_type'    => 'reseller_product',
				'post_status'  => 'publish',
				'post_content' => 'this is a product',
			]
		);

		$post_ids = [ $post_id ];

		$bulk_restore = new Bulk_Restore();

		$redirect_to = $bulk_restore->bulk_action_handler( '', 'restore_product_data', $post_ids );

		$errors = rstore_get_option( 'errors', [] );

		$this->assertEquals( 1, count( $errors ) );
		$this->assertEquals( '`%s` does not have a valid product id.', $errors[0]->get_error_message() );
		$this->assertEmpty( $redirect_to );

	}

	/**
	 * @testdox Given product id not found in the api, the bulk_action_handler should set an error.
	 */
	public function test_bulk_action_handler_invalid_product() {

		rstore_update_option( 'pl_id', 1592 );

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		$post = Tests\Helper::create_product( 'WordPress', 'invalid-product-id' );

		$post_ids = [ $post->ID ];

		$bulk_restore = new Bulk_Restore();

		$redirect_to = $bulk_restore->bulk_action_handler( '', 'restore_product_data', $post_ids );

		$errors = rstore_get_option( 'errors', [] );

		$this->assertEquals( 1, count( $errors ) );
		$this->assertEquals( '`%s` is not a valid product.', $errors[0]->get_error_message() );
		$this->assertEmpty( $redirect_to );

	}

	/**
	 * @testdox Given a user cannot edit posts, the bulk_action_handler should set an error.
	 */
	public function test_bulk_action_handler_invalid_user() {

		rstore_update_option( 'pl_id', 1592 );

		$post = Tests\Helper::create_product();

		$post_ids = [ $post->ID ];

		$bulk_restore = new Bulk_Restore();

		$redirect_to = $bulk_restore->bulk_action_handler( '', 'restore_product_data', $post_ids );

		$errors = rstore_get_option( 'errors', [] );

		$this->assertEquals( 1, count( $errors ) );
		$this->assertEquals( 'Current user cannot modify post.', $errors[0]->get_error_message() );
		$this->assertEmpty( $redirect_to );

	}

	/**
	 * @testdox Given product is not imported, the bulk_action_handler should set an error.
	 */
	public function test_bulk_action_handler_product_not_imported() {

		rstore_update_option( 'pl_id', 1592 );
		new Post_Type();
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

		$post_ids = [ $post->ID ];

		$bulk_restore = new Bulk_Restore();

		$redirect_to = $bulk_restore->bulk_action_handler( '', 'restore_product_data', $post_ids );

		$errors = rstore_get_option( 'errors', [] );

		$this->assertEquals( 1, count( $errors ) );
		$this->assertEquals( '`%s` must be imported as a product post before it can be reset.', $errors[0]->get_error_message() );
		$this->assertEquals( '', $redirect_to );

	}

}
