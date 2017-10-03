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

		// display title.
		$this->expectOutputRegex( '/<h3 class="widget-title">WordPress Hosting</h3>/' );

		// display price.
		$this->expectOutputRegex( '/<p class="rstore-pricing"><span class="rstore-price rstore-has-sale-price"><del>$70.00<\/del> $50.00<\/span> / per year<\/p>/' );

		// display button.
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

		$this->expectOutputRegex( '/<select id="widget-rstore_product--post_id" name="widget-rstore_product\[\]\[post_id\]" class="widefat" style="width:100%;">/' );
		$this->expectOutputRegex( '/<select id="widget-rstore_product--image_size" name="widget-rstore_product\[\]\[image_size\]" class="widefat" style="width:100%;">/' );
		$this->expectOutputRegex( '/<input type="checkbox" id="widget-rstore_product--show_title" name="widget-rstore_product\[\]\[show_title\]" value="1" class="checkbox"  checked=\'checked\'>/' );
		$this->expectOutputRegex( '/<input type="checkbox" id="widget-rstore_product--show_content" name="widget-rstore_product\[\]\[show_content\]" value="1" class="checkbox"  checked=\'checked\'>/' );
		$this->expectOutputRegex( '/<input type="checkbox" id="widget-rstore_product--show_price" name="widget-rstore_product\[\]\[show_price\]" value="1" class="checkbox"  checked=\'checked\'>/' );
		$this->expectOutputRegex( '/<input type="checkbox" id="widget-rstore_product--redirect" name="widget-rstore_product\[\]\[redirect\]" value="1" class="checkbox"  checked=\'checked\'>/' );
		$this->expectOutputRegex( '/<input type="text" id="widget-rstore_product--button_label" name="widget-rstore_product\[\]\[button_label\]" value="" class="widefat">/' );

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
