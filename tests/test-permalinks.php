<?php
/**
 * GoDaddy Reseller Store permalinks class tests
 */

namespace Reseller_Store;

/**
 * @action Patches the WordPress function filter_input
 *
 * @param string $type  The type of input.
 * @param string $variable_name Name of a variable to get.
 *
 * @return bool
 */
function filter_input( $type, $variable_name ) {

	if ( isset( $_POST[ $variable_name ] ) ) {

		return $_POST[ $variable_name ];

	}

}

/**
 * @action Patches the WordPress function check_admin_referer
 *
 * @return bool
 */
function check_admin_referer() {
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
	 * @testdox Test admin init function saves permalinks.
	 */
	public function test_init() {

		new Post_Type();
		do_action( 'init' );

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);

		wp_set_current_user( $user_id );
		set_current_screen( 'options-permalink' );

		$_POST['_wpnonce'] = wp_create_nonce( 'update-permalink' );

		$_POST['permalink_structure'] = 'new-products';

		rstore_update_option(
			'permalinks', [
				'product_base' => 'products',
			]
		);

		$permalinks = new Permalinks();

		$permalinks->init();

		$options = rstore_get_option( 'permalinks', [] );

		$this->assertEquals( '', $options['product_base'] );

	}

	/**
	 * @testdox Given no nonce init should not update permalinks.
	 */
	public function test_invalid_init() {

		rstore_update_option(
			'permalinks', [
				'product_base' => 'original-value',
			]
		);

		$permalinks = new Permalinks();

		$permalinks->init();

		$options = rstore_get_option( 'permalinks', [] );

		$this->assertEquals( 'original-value', $options['product_base'] );

	}

	/**
	 * @testdox Given section function should render.
	 */
	public function test_section_function() {

		new Post_Type();
		do_action( 'init' );

		$permalinks = new Permalinks();

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
