<?php
/**
 * GoDaddy Reseller Store Product Widget tests
 */

namespace Reseller_Store;

final class TestWidgetProduct extends TestCase {

	/**
	 * @testdox Test Product widgets exist.
	 */
	function test_basics() {

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Widgets\Product' ),
			'Class \Widgets\Product is not found'
		);

	}

	/**
	 * @testdox Given a valid instance the widget should render
	 */
	function test_widget() {

		$widget = new Widgets\Product();

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

		$this->expectOutputRegex( '/<button class="rstore-add-to-cart button" data-id="wordpress-basic" data-quantity="1" data-redirect="false">Add to cart<\/button>/' );

	}

	/**
	 * @testdox Given a new instance the instance should update
	 */
	function test_widget_update() {

		$widget = new Widgets\Product();

		$old_instance = [
			'post_id'    => 0,
			'show_title' => true,
			'show_content' => true,
			'show_price' => true,
			'redirect' => true,
			'button_label' => '',
			'text_cart' => '',
			'image_size' => '',
		];

		$new_instance = [
			'post_id'    => 100,
			'show_title' => false,
			'show_content' => false,
			'show_price' => false,
			'redirect' => false,
			'button_label' => 'button_label 1',
			'text_cart' => 'text_cart 1',
			'image_size' => 'image_size 1',
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

		$widget = new Widgets\Product();

		$instance = [
			'post_id'    => 0,
			'show_title' => true,
			'show_content' => true,
			'show_price' => true,
			'redirect' => true,
			'button_label' => '',
			'image_size' => '',
		];

		$widget->form( $instance );

		$this->expectOutputRegex( '/<input type="checkbox" id="widget-rstore_product--redirect" name="widget-rstore_product\[\]\[redirect\]" value="1" class="checkbox"  checked=\'checked\'>/' );

	}

	/**
	 * @testdox Given an a product widget classes filter it should render
	 */
	function test_widget_filter() {

		add_filter(
			'rstore_product_widget_classes', function( $title ) {
				return [ 'product' ];
			}
		);

		$widget = new Widgets\Product();

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
		$this->expectOutputRegex( '/<div class="before_widget product">/' );

	}


}
