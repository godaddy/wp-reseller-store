<?php
/**
 * GoDaddy Reseller Store Widget tests
 */

namespace Reseller_Store;

final class TestWidget extends TestCase {

	/**
	 * Test widgets exist.
	 */
	function test_basics() {

		// Check cart widget presence.
		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Widgets\Cart' ),
			'Class \Widgets\Cart is not found'
		);

		// Check Domain_Search widget presence.
		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Widgets\Domain_Search' ),
			'Class \Widgets\Domain_Search is not found'
		);

		// Check Product widget presence.
		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Widgets\Product' ),
			'Class \Widgets\Product is not found'
		);

	}

	/**
	 * Test that all required actions and filters are added as expected.
	 */
	function test_init() {

		new Widgets();

		$this->do_action_validation( 'widgets_init', [ __NAMESPACE__ . '\Widgets', 'register_widgets' ] );

	}


	/**
	 * Test for register_widget function.
	 */
	function test_register_widget() {

		$this->assertTrue(
			method_exists( __NAMESPACE__ . '\Widgets', 'register_widgets' ),
			'Method Widgets::register_widgets is not found'
		);

		Widgets::register_widgets();

		global $wp_widget_factory;

	}

	/**
	 * Test the product widget.
	 */
	function test_product_widget() {

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
		$this->expectOutputRegex( '/<button class="rstore-add-to-cart button" data-id="wordpress_hosting" data-quantity="1" data-redirect="false">Add to cart<\/button>/' );

	}

	/**
	 * Test the cart widget.
	 */
	function test_cart_widget() {

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
	 * @testdox Test the domain search widget.
	 */
	function test_domain_search_widget() {

		$widget = new Widgets\Domain_Search();

		$post = Tests\Helper::create_product();

		$instance = [
			'post_id'    => $post->ID,
			'image_size' => 'full',
			'show_title' => true,
		];

		$args = [
			'before_widget' => '<div class="before_widget">',
			'after_widget' => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		];

		$widget->widget( $args, $instance );

		// display domain search.
		$this->expectOutputRegex( '/<div class="rstore-domain-search"><\/div>/' );

	}


	/**
	 * @testdox Test login widget
	 */
	function test_login_widget() {

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

		// display domain search.
		$this->expectOutputRegex( '/<span class="rstore-welcome-message">Welcome<\/span>/' );
		$this->expectOutputRegex( '/<a class="rstore-logout-button" href="https:\/\/sso.secureserver.net\/logout\?plid=0&realm=idp&app=www" rel="nofollow">Log Out<\/a>/' );
	}
}
