<?php
/**
 * GoDaddy Reseller Store Cart Widget tests
 */

namespace Reseller_Store;

final class TestWidgetCart extends TestCase {

	/**
	 * @testdox Test Cart widgets exist.
	 */
	function test_basics() {

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Widgets\Cart' ),
			'Class \Widgets\Cart is not found'
		);

	}

	/**
	 * @testdox Given a valid instance the widget should render
	 */
	function test_widget() {

		$widget = new Widgets\Cart();

		rstore_update_option( 'pl_id', 12345 );

		$post = Tests\Helper::create_product();

		$instance = array(
			'post_id'    => $post->ID,
			'image_size' => 'full',
			'show_title' => true,
		);

		$args = array(
			'before_widget' => '<div class="before_widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);

		$this->assertRegExp(
			'/<a href="https:\/\/cart.secureserver.net\/go\/checkout\/\?plid=12345">\s+View Cart \(<span class="rstore-cart-count">0<\/span>\)\s+<\/a>/',
			$widget->widget( $args, $instance )
		);

	}

	/**
	 * @testdox Given a new instance the instance should update
	 */
	function test_widget_update() {

		$widget = new Widgets\Cart();

		$old_instance = array(
			'title'        => '',
			'button_label' => '',
		);

		$new_instance = array(
			'title'        => 'title 1',
			'button_label' => 'button_label 1',
		);

		$instance = $widget->update( $new_instance, $old_instance );

		foreach ( $instance as $key => $value ) {
			$this->assertEquals( $instance[ $key ], $new_instance[ $key ] );
		}

	}

	/**
	 * @testdox Given an instance the form should render
	 */
	function test_widget_form() {

		$widget = new Widgets\Cart();

		$instance = array(
			'title'        => 'aaa',
			'button_label' => 'bbb',
		);

		$widget->form( $instance );

		foreach ( $instance as $key => $value ) {
			$this->expectOutputRegex( '/<input type="text" id="widget-rstore_cart--' . $key . '" name="widget-rstore_cart\[\]\[' . $key . '\]" value="' . $value . '" class="widefat">/' );
		}

	}

	/**
	 * @testdox Given an a cart widget classes filter it should render
	 */
	function test_widget_filter() {

		add_filter(
			'rstore_cart_widget_classes',
			function( $title ) {
				return array( 'cart' );
			}
		);

		$widget = new Widgets\Cart();

		rstore_update_option( 'pl_id', 12345 );

		$post = Tests\Helper::create_product();

		$instance = array(
			'post_id'    => $post->ID,
			'image_size' => 'full',
			'show_title' => true,
		);

		$args = array(
			'before_widget' => '<div class="before_widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);

		$this->assertRegExp(
			'/<div class="before_widget cart">/',
			$widget->widget( $args, $instance )
		);

	}


}
