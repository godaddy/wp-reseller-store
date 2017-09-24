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

		$instance = [
			'post_id'    => $post->ID,
			'image_size' => 'full',
			'show_title' => true,
		];

		$args = [
			'before_widget' => '<div class="before_widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		];

		$widget->widget( $args, $instance );

		// display main div tag.
		$this->expectOutputRegex( '/<div class="rstore-view-cart">/' );

		// display view cart link.
		$this->expectOutputRegex( '/<a href="https:\/\/cart.secureserver.net\/">\s+View Cart \(<span class="rstore-cart-count">0<\/span>\)\s+<\/a>/' );

	}

	/**
	 * @testdox Given a new instance the instance should update
	 */
	function test_widget_update() {

		$widget = new Widgets\Cart();

		$old_instance = [
			'title'    => '',
			'button_label' => '',
		];

		$new_instance = [
			'title'    => 'title 1',
			'button_label' => 'button_label 1',
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

		$widget = new Widgets\Cart();

		$instance = [
			'title'    => 'aaa',
			'button_label' => 'bbb',
		];

		$widget->form( $instance );

		foreach ( $instance as $key => $value ) {
			$this->expectOutputRegex( '/<input type="text" id="widget-rstore_cart--' . $key . '" name="widget-rstore_cart\[\]\[' . $key . '\]" value="' . $value . '" class="widefat">/' );
		}

	}

	/**
	 * @testdox Given an a cart widdget classes filter it should render
	 */
	function test_widget_filter() {

		add_filter(
			'rstore_cart_widget_classes', function( $title ) {
				return [ 'cart' ];
			}
		);

		$widget = new Widgets\Cart();

		rstore_update_option( 'pl_id', 12345 );

		$post = Tests\Helper::create_product();

		$instance = [
			'post_id'    => $post->ID,
			'image_size' => 'full',
			'show_title' => true,
		];

		$args = [
			'before_widget' => '<div class="before_widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		];

		$widget->widget( $args, $instance );

		// display main div tag.
		$this->expectOutputRegex( '/<div class="before_widget cart">/' );

	}


}
