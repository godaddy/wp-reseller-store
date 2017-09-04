<?php
/**
 * GoDaddy Reseller Store Shortcode tests
 */

namespace Reseller_Store;

final class TestShortcodes extends TestCase {

	/**
	 * Setup.
	 */
	function setUp() {

		parent::setUp();
		new Shortcodes();

	}

	/**
	 * @testdox Given a domain search shortcode it should generate html
	 */
	function test_domain_search() {

		$content = '[rstore-domain-search]';

		do_shortcode( $content );

		$this->expectOutputRegex( '/<div class="rstore-domain-search" data-plid= data-text-placeholder="Find your perfect domain name" data-text-search="Search" data-text-available="Congrats, your domain is available!" data-text-not-available="Sorry that domain is taken" data-text-cart="Continue to Cart" data-text-select="Select" data-text-selected="Selected"><\/div>/' );
	}

	/**
	 * @testdox Given a cart shortcode it should generate html
	 */
	function test_view_cart() {

		$content = '[rstore-cart-button]';

		do_shortcode( $content );

		$this->expectOutputRegex( '/<a href="https:\/\/cart\.secureserver\.net\/">\n.*View Cart \(<span class="rstore-cart-count">0<\/span>\)/' );
	}

	/**
	 * @testdox Given a cart shortcode with params it should generate html
	 */
	function test_view_cart_with_params() {

		$content = '[rstore-cart-button title="Cart" button_label="button label"]';

		do_shortcode( $content );

		$this->expectOutputRegex( '/<a href="https:\/\/cart\.secureserver\.net\/">\n.*button label \(<span class="rstore-cart-count">0<\/span>\)/' );
	}


	/**
	 * @testdox Given a prouct shortcode without params it should display message
	 */
	function test_product_without_params() {

		$content = '[rstore-product]';

		do_shortcode( $content );

		$this->expectOutputRegex( '/Post id is not valid\./' );
	}

	/**
	 * @testdox Given a prouct shortcode with invalid post id it should display message
	 */
	function test_product_with_invalid_post_id() {

		$content = '[rstore-product post_id=12]';

		do_shortcode( $content );

		$this->expectOutputRegex( '/Post id is not valid\./' );
	}

	/**
	 * @testdox Given a valid prouct shortcode it should generate html
	 */
	function test_product_with_valid_post_id() {

		$post = Tests\Helper::create_product( 'Test Product' );

		$content = '[rstore-product post_id=' . $post->ID . ']';

		do_shortcode( $content );

		$this->expectOutputRegex( '/Test Product/' );
	}

	/**
	 * @testdox Given a valid product shortcode with all blank params it should generate nothing
	 */
	function test_product_with_all_params() {

		$post = Tests\Helper::create_product( 'Another Product' );

		$content = '[rstore-product
      post_id=' . $post->ID . '
      show_title=0
      show_content=0
      show_price=0
      button_label=""
      image_size=none
      ]';

		do_shortcode( $content );

		$this->expectOutputString( '' );
	}

	/**
	 * @testdox Given a valid product shortcode with redirct=1 params it should redirect truthy data attribute
	 */
	function test_product_with_redirect_1_param() {

		$post = Tests\Helper::create_product( 'Another Product good' );

		$content = '[rstore-product
      post_id=' . $post->ID . '
      redirect=1
      ]';

		do_shortcode( $content );

		$this->expectOutputRegex( '/data-redirect="true"/' );
	}

	/**
	 * @testdox Given a valid product shortcode with redirect=0 params it should redirect falsy data attribute
	 */
	function test_product_with_redirect_0_param() {

		$post = Tests\Helper::create_product( 'Another Product good' );

		$content = '[rstore-product
      post_id=' . $post->ID . '
      redirect=0
      ]';

		do_shortcode( $content );

		$this->expectOutputRegex( '/data-redirect="false"/' );
	}

	/**
	 * @testdox Given a valid login shortcode it should generate the login html
	 */
	function test_login() {

		$post = Tests\Helper::create_product( 'Another Product good' );

		$content = '[rstore-login
      welcome_message="aaaa"
      login_button_text="bbbb"
      logout_button_text="cccc"
      ]';

		do_shortcode( $content );

		$this->expectOutputRegex( '/<a class="login-link" href="https:\/\/mya.secureserver.net\/\?plid=0&realm=idp&app=www"/' );
		$this->expectOutputRegex( '/<span class="welcome-message">aaaa<\/span>/' );
		$this->expectOutputRegex( '/<a class="logout-link" href="https:\/\/sso.secureserver.net\/logout\?plid=0&realm=idp&app=www" rel="nofollow">cccc<\/a>/' );

	}

}
