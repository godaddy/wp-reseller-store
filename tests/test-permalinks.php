<?php
/**
 * GoDaddy Reseller Store permalinks class tests
 */

namespace Reseller_Store;

/**
 * @action Patches the WordPress function wp_verify_nonce
 *
 * @param string $nonce  The nonce value.
 * @param string $action Optional action.
 *
 * @return bool
 */
function wp_verify_nonce( $nonce, $action ) {
	return true;
}

final class TestPermalinks extends TestCase {

	/**
	 * @testdox Test that Permalinks class exists.
	 */
	public function test_basics() {

		new Permalinks();

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Permalinks' ) );

	}

	/**
	 * @testdox Test admin init function.
	 */
	public function test_admin_init_action() {

		new Post_Type();
		do_action( 'init' );

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );
		set_current_screen( 'options-permalink' );

		$_REQUEST['_wpnonce'] = wp_create_nonce( 'update-permalink' );

		$_POST['permalink_structure'] = 'products';

		rstore_update_option( 'permalinks', [
			'product_base' => 'products',
		] );

		$permalinks = new Permalinks();

		do_action( 'admin_init' );

		$permalinks->section();

		$this->expectOutputRegex( '/<p>These settings control the permalinks used specifically for Reseller Store products.<\/p>/' );

	}

	/**
	 * @testdox Given the action admin_enqueue_scripts for non-admin the styles and scripts should not render
	 */
	public function test_wp_enqueue_scripts() {

		new Permalinks();

		do_action( 'admin_enqueue_scripts' );

		$this->assertFalse( wp_script_is( 'rstore-admin-permalinks' ), 'done' );

	}

	/**
	 * @testdox Given the action admin_enqueue_scripts the styles and scripts should render
	 */
	public function test_admin_enqueue_scripts() {

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );
		set_current_screen( 'options-permalink.php' );

		$_SERVER['REQUEST_URI'] = 'options-permalink.php';

		new Permalinks();

		do_action( 'admin_enqueue_scripts' );

		$this->assertTrue( wp_script_is( 'rstore-admin-permalinks' ), 'done' );

	}

}
