<?php
/**
 * GoDaddy Reseller Store Admin Notice class tests
 */

namespace Reseller_Store;

final class TestAdminNotice extends \WP_Ajax_UnitTestCase {


	/**
	 * Tear Down.
	 */
	public function tearDown() {

		parent::tearDown();

		$_SERVER['REQUEST_URI'] = '';
		unset( $_REQUEST );

	}

	/**
	 * @testdox Test that Admin Notice class exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Admin_Notices' ) );

	}

	/**
	 * @testdox Given the action admin_enqueue_scripts and not on edit screen the styles and scripts should not render
	 */
	public function test_admin_enqueue_scripts_no_screen() {

		$error = new \WP_Error();

		rstore_error( $error );

		new Admin_Notices();

		do_action( 'admin_enqueue_scripts' );

		$this->assertFalse( wp_script_is( 'rstore-admin-notice' ), 'done' );

	}


	/**
	 * @testdox Given the action admin_enqueue_scripts the styles and scripts should render
	 */
	public function test_admin_enqueue_scripts() {

		set_current_screen( 'edit.php?post_type=reseller_product' );

		$_SERVER['REQUEST_URI'] = 'edit.php?post_type=reseller_product';

		$error = new \WP_Error();

		rstore_error( $error );

		new Admin_Notices();

		do_action( 'admin_enqueue_scripts' );

		$this->assertTrue( wp_script_is( 'rstore-admin-notice' ), 'done' );

	}

	/**
	 * @testdox Given the action admin_notices and query string param the success div should render
	 */
	public function test_restore_notice() {

		set_current_screen( 'edit.php' );

		$_SERVER['REQUEST_URI']         = 'edit.php?post_type=reseller_product';
		$_REQUEST['bulk_restore_posts'] = 3;

		new Admin_Notices();

		do_action( 'admin_notices' );

		$this->expectOutputRegex( '/<div id="rstore-update-success" class="notice notice-success is-dismissible"><p>Finished restoring products. 3 products updated.<\/p><\/div>/' );

	}

	/**
	 * @testdox Given the action admin_notices and error data the error div should render
	 */
	public function test_error_notices() {

		set_current_screen( 'edit.php' );

		$_SERVER['REQUEST_URI'] = 'edit.php?post_type=reseller_product';

		$error = new \WP_Error();

		rstore_error( $error );

		new Admin_Notices();

		do_action( 'admin_notices' );

		$this->expectOutputRegex( '/<div id="rstore-update-error" class="notice notice-error is-dismissible"><p><\/p><\/div>/' );

	}

	/**
	 * Helper function to make ajax call.
	 *
	 * @param string $action Ajax action name.
	 * @return mixed response
	 */
	protected function callAjax( $action ) {

		try {
			new Admin_Notices();
			$this->_handleAjax( $action );
		} catch ( WPAjaxDieContinueException $e ) {

			echo 'error';
		}

		return json_decode( $this->_last_response );

	}

	/**
	 * @testdox Given dismiss_admin_notice and invalid nonce an error is returned
	 *
	 * @expectedException WPAjaxDieContinueException
	 */
	public function test_dismiss_admin_notice_without_nonce() {

		$error = new \WP_Error();

		rstore_error( $error );

		$this->callAjax( 'rstore_dismiss_admin_notice' );

	}

	/**
	 * @testdox Given dismiss_admin_notice and valid nonce clears error message
	 *
	 * @expectedException WPAjaxDieContinueException
	 */
	public function test_dismiss_admin_notice() {

		$key = rstore_prefix( 'notice-' . get_current_user_id() );

		$_POST['nonce'] = wp_create_nonce( $key );

		$this->callAjax( 'rstore_dismiss_admin_notice' );

	}
}
