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

	/**
	 * @testdox Given setup content the header should render
	 */
	public function test_content_render() {

		$setup = new Setup();

		$setup->content();

		$this->expectOutputRegex( '/<h2>Let&#039;s setup your Reseller Store.<\/h2>/' );
	}

	/**
	 * @testdox Given pl_id it should install
	 */
	public function test_setup_install() {

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		Setup::install( 100 );

		$pl_id = rstore_get_option( 'pl_id' );

		$this->assertEquals( 100, $pl_id );
	}

}
