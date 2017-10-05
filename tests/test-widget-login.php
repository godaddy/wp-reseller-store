<?php
/**
 * GoDaddy Reseller Store Login Widget tests
 */

namespace Reseller_Store;

final class TestWidgetLogin extends TestCase {

	/**
	 * @testdox Test Login widgets exist.
	 */
	function test_basics() {

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Widgets\Login' ),
			'Class \Widgets\Login is not found'
		);

	}

	/**
	 * @testdox Given a valid instance the widget should render
	 */
	function test_widget() {

		$widget = new Widgets\Login();

		$instance = [
			'title'    => 'login',
			'welcome_message' => 'Welcome',
			'login_button_text' => 'log in',
			'login_button_text' => 'log out',
		];

		$args = [
			'before_widget' => '<div class="before_widget">',
			'after_widget' => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		];

		$widget->widget( $args, $instance );

		$this->expectOutputRegex( '/<a class="logout-link" href="https:\/\/sso.secureserver.net\/logout\?plid=0&realm=idp&app=www" rel="nofollow">Log Out<\/a>/' );
	}

	/**
	 * @testdox Given a new instance the instance should update
	 */
	function test_widget_update() {

		$widget = new Widgets\Login();

		$old_instance = [
			'title'    => '',
			'welcome_message' => '',
			'login_button_text' => '',
			'logout_button_text' => '',
		];

		$new_instance = [
			'title'    => 'aaa',
			'welcome_message' => 'bbb',
			'login_button_text' => 'ccc',
			'logout_button_text' => 'ddd',
		];

		$instance = $widget->update( $new_instance, $old_instance );

		foreach ( $instance as $key => $value ) {
			$this->assertEquals( $instance[ $key ],  $new_instance[ $key ] );
		}

	}

	/**
	 * @testdox Given an instance the form should render
	 */
	function test_widget_form() {

		$widget = new Widgets\Login();

		$instance = [
			'title'    => 'aaa',
			'welcome_message' => 'bbb',
			'login_button_text' => 'ccc',
			'logout_button_text' => 'ddd',
		];

		$widget->form( $instance );

		foreach ( $instance as $key => $value ) {
			$this->expectOutputRegex( '/<input type="text" id="widget-rstore_login--' . $key . '" name="widget-rstore_login\[\]\[' . $key . '\]" value="' . $value . '" class="widefat">/' );
		}

	}


}
