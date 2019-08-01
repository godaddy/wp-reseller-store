<?php
/**
 * GoDaddy Reseller Store Admin Notice class tests
 */

namespace Reseller_Store;

final class TestSettingsAjax extends \WP_Ajax_UnitTestCase {


	/**
	 * Tear Down.
	 */
	public function tearDown() {

		parent::tearDown();

		$_SERVER['REQUEST_URI'] = '';
		unset( $_REQUEST );
		unset( $_POST );

	}

	/**
	 * Helper function to make ajax call.
	 *
	 * @param string $action Ajax action name.
	 * @return mixed response
	 */
	protected function callAjax( $action ) {

		try {
			new Settings();
			$this->_handleAjax( $action );
		} catch ( WPAjaxDieContinueException $e ) {

			echo 'error';
		}

		return json_decode( $this->_last_response );

	}

	/**
	 * @testdox Given dismiss_admin_notice and valid nonce clears error message
	 *
	 * @expectedException WPAjaxDieContinueException
	 */
	public function test_save_bad_nonce() {

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		$_POST['nonce']      = 'bad nonce';
		$_POST['active_tab'] = 'product_options';

		$result = $this->callAjax( 'rstore_settings_save' );

		$this->assertEquals( 'error', $result );

	}

	/**
	 * @testdox Given dismiss_admin_notice and valid nonce clears error message
	 *
	 * @expectedException WPAjaxDieContinueException
	 */
	public function test_save_unauthorized_user() {

		$user_id = $this->factory->user->create(
			array(
				'role' => 'author',
			)
		);
		wp_set_current_user( $user_id );

		$_POST['nonce']      = wp_create_nonce( 'rstore_settings_save' );
		$_POST['active_tab'] = 'product_options';

		$result = $this->callAjax( 'rstore_settings_save' );

		$this->assertEquals( 'error', $result );

	}

	/**
	 * @testdox Given dismiss_admin_notice and invalid table creates error message
	 *
	 * @expectedException WPAjaxDieContinueException
	 */
	public function test_save_invalid_test() {

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		$_POST['nonce']      = wp_create_nonce( 'rstore_settings_save' );
		$_POST['active_tab'] = 'invalid';

		$result = $this->callAjax( 'rstore_settings_save' );

		$this->assertEquals( 'success', $result );

	}

	/**
	 * @testdox Given dismiss_admin_notice and valid nonce clears error message
	 *
	 * @expectedException WPAjaxDieContinueException
	 */
	public function test_save() {

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		$_POST['nonce']      = wp_create_nonce( 'rstore_settings_save' );
		$_POST['active_tab'] = 'product_options';

		$result = $this->callAjax( 'rstore_settings_save' );

		$this->assertEquals( 'success', $result );

	}

	/**
	 * @testdox Given import ajax event product data should import
	 */
	public function test_import_click_author() {

		rstore_update_option( 'pl_id', 1592 );

		$user_id = $this->factory->user->create(
			array(
				'role' => 'author',
			)
		);
		wp_set_current_user( $user_id );

		$_POST['nonce'] = wp_create_nonce( 'rstore_settings_install' );

		$this->callAjax( 'rstore_settings_import' );

		$this->assertTrue( rstore_has_products() );

	}

	/**
	 * @testdox Given import ajax event product data should import
	 */
	public function test_import_click_subscriber() {

		$user_id = $this->factory->user->create(
			array(
				'role' => 'subscriber',
			)
		);
		wp_set_current_user( $user_id );

		$_POST['nonce'] = wp_create_nonce( 'rstore_settings_install' );

		$this->callAjax( 'rstore_settings_import' );

		$this->assertFalse( rstore_has_products() );

	}

}
