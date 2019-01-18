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

		echo $widget->widget( $args, $instance );

		$this->expectOutputRegex( '/<div><button class="rstore-add-to-cart button btn btn-primary" data-id="wordpress-basic" data-quantity="1" data-redirect="true">Add to cart<\/button><\/div>/' );

	}

	/**
	 * @testdox Given a classic layout_type the widget should render
	 */
	function test_widget_classic() {

		$widget = new Widgets\Product();

		$post = Tests\Helper::create_product();

		$instance = [
			'post_id'     => $post->ID,
			'layout_type' => 'classic',
		];

		$args = [
			'before_widget' => '<div class="before_widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		];

		echo $widget->widget( $args, $instance );

		$this->expectOutputRegex( '/<div><button class="rstore-add-to-cart button btn btn-primary" data-id="wordpress-basic" data-quantity="1" data-redirect="true">Add to cart<\/button><\/div>/' );

	}

	/**
	 * @testdox Given a valid instance and redirect=false the widget should render with redirect=false
	 */
	function test_widget_no_redirect() {

		$widget = new Widgets\Product();

		$post = Tests\Helper::create_product();

		$instance = [
			'post_id'    => $post->ID,
			'image_size' => 'full',
			'show_title' => true,
			'redirect'   => false,
		];

		$args = [
			'before_widget' => '<div class="before_widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		];

		echo $widget->widget( $args, $instance );

		$this->expectOutputRegex( '/<button class="rstore-add-to-cart button btn btn-primary" data-id="wordpress-basic" data-quantity="1" data-redirect="false">Add to cart<\/button>/' );

	}

	/**
	 * @testdox Given `rstore_is_widget` filter the product widget should render
	 */
	function test_widget_with_rstore_is_widget_filter() {

		add_filter(
			'rstore_is_widget',
			function() {
				return true;
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

		$this->expectOutputRegex( '/<button class="rstore-add-to-cart button btn btn-primary" data-id="wordpress-basic" data-quantity="1" data-redirect="true">Add to cart<\/button>/' );

	}

	/**
	 * @testdox Given `rstore_is_widget` filter and no product will return `Post id is not valid.`
	 */
	function test_widget_with_filter_and_no_product() {

		add_filter(
			'rstore_is_widget',
			function() {
				return true;
			}
		);

		$widget = new Widgets\Product();

		$instance = [
			'post_id'    => 0,
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

		$this->expectOutputRegex( '/Post id is not valid./' );

	}


	/**
	 * @testdox Given a new instance the instance should update
	 */
	function test_widget_update() {

		$widget = new Widgets\Product();

		$old_instance = [
			'post_id'        => 0,
			'show_title'     => true,
			'show_content'   => true,
			'show_price'     => true,
			'redirect'       => true,
			'button_label'   => '',
			'text_cart'      => '',
			'image_size'     => '',
			'text_more'      => '',
			'content_height' => 0,
			'layout_type'    => 'classic',
		];

		$new_instance = [
			'post_id'        => 100,
			'show_title'     => false,
			'show_content'   => false,
			'show_price'     => false,
			'redirect'       => false,
			'button_label'   => 'button_label 1',
			'text_cart'      => 'text_cart 1',
			'image_size'     => 'image_size 1',
			'text_more'      => 'text_more 1',
			'content_height' => 100,
			'layout_type'    => 'default',
		];

		$instance = $widget->update( $new_instance, $old_instance );

		foreach ( $instance as $key => $value ) {
			$this->assertEquals( $instance[ $key ], $new_instance[ $key ] );
		}

	}

	/**
	 * @testdox Given an instance the form should render
	 */
	function test_widget_form() {

		$post = Tests\Helper::create_product( 'product one' );
		Tests\Helper::create_product( 'product two' );

		$widget = new Widgets\Product();

		$instance = [
			'post_id'      => $post->ID,
			'show_title'   => true,
			'show_content' => true,
			'show_price'   => true,
			'redirect'     => true,
			'button_label' => '',
			'image_size'   => '',
		];

		$widget->form( $instance );

		$this->expectOutputRegex( '/<input type="checkbox" id="widget-rstore_product--redirect" name="widget-rstore_product\[\]\[redirect\]" value="1" class="checkbox"  checked=\'checked\'>/' );

	}

	/**
	 * @testdox Given an instance without any products the form should render
	 */
	function test_widget_form_no_product() {

		$widget = new Widgets\Product();

		$instance = [
			'post_id'      => 0,
			'show_title'   => true,
			'show_content' => true,
			'show_price'   => true,
			'redirect'     => true,
			'button_label' => '',
			'image_size'   => '',
		];

		$widget->form( $instance );

		$this->expectOutputRegex( '/<input type="checkbox" id="widget-rstore_product--redirect" name="widget-rstore_product\[\]\[redirect\]" value="1" class="checkbox"  checked=\'checked\'>/' );

	}

	/**
	 * @testdox Given a product widget classes filter it should render
	 */
	function test_widget_filter() {

		add_filter(
			'rstore_product_widget_classes',
			function( $title ) {
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

		echo $widget->widget( $args, $instance );

		// display main div tag.
		$this->expectOutputRegex( '/<div class="before_widget product">/' );

	}

	/**
	 * @testdox Given `the_content` filter the product widget should render
	 */
	function test_widget_in_content_filter() {

		add_filter(
			'the_content',
			function() {

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

				ob_start();

				echo $widget->widget( $args, $instance );

				return ob_get_clean();

			}
		);

		echo apply_filters( 'the_content', 'test' );

		$this->expectOutputRegex( '/<h3 class="widget-title">WordPress Hosting<\/h3>/' );

	}

}
