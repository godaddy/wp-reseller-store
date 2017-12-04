<?php
/**
 * GoDaddy Reseller Store test setup class
 */

namespace Reseller_Store;

final class TestSetup extends TestCase {

	/**
	 * @testdox Test that Setup class exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Setup' ) );

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
		set_current_screen( 'admin.php?page=reseller-store-setup' );

		$_SERVER['REQUEST_URI'] = 'admin.php?page=reseller-store-setup';

		new Setup();

		do_action( 'admin_enqueue_scripts' );

		$this->assertTrue( wp_script_is( 'rstore-admin-permalinks' ), 'done' );

	}

}
