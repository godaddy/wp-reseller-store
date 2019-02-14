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
	 * @testdox Given a legacy domain search shortcode it should generate html
	 */
	function test_domain_search_legacy() {

		$this->assertContains(
			'<div class="widget rstore-domain rstore_domain_placeholder"><div class="rstore-domain-search" data-plid="" data-page_size="5" data-text_placeholder="Find your perfect domain name" data-text_search="Search" data-text_available="Congrats, {domain_name} is available!" data-text_not_available="Sorry, {domain_name} is taken." data-text_cart="Continue to cart" data-text_select="Select" data-text_selected="Selected">Domain Search</div></div>',
			do_shortcode( '[rstore-domain-search]' )
		);

	}

	/**
	 * @testdox Given a domain search shortcode it should generate html
	 */
	function test_domain_search() {

		$this->assertContains(
			do_shortcode( '[rstore_domain_search]' ),
			'<div class="widget rstore-domain rstore_domain_placeholder"><div class="rstore-domain-search" data-plid="" data-page_size="5" data-text_placeholder="Find your perfect domain name" data-text_search="Search" data-text_available="Congrats, {domain_name} is available!" data-text_not_available="Sorry, {domain_name} is taken." data-text_cart="Continue to cart" data-text_select="Select" data-text_selected="Selected">Domain Search</div></div>'
		);

	}

	/**
	 * @testdox Given a cart shortcode it should generate html
	 */
	function test_view_cart() {

		$this->assertRegExp(
			'/<a href="https:\/\/cart\.secureserver\.net\/go\/checkout\/">\n.*View Cart \(<span class="rstore-cart-count">0<\/span>\)/',
			do_shortcode( '[rstore_cart_button]' )
		);

	}

	/**
	 * @testdox Given a cart shortcode with params it should generate html
	 */
	function test_view_cart_with_params() {

		$this->assertRegExp(
			'/<a href="https:\/\/cart\.secureserver\.net\/go\/checkout\/">\n.*button label \(<span class="rstore-cart-count">0<\/span>\)/',
			do_shortcode( '[rstore_cart_button title="Cart" button_label="button label"]' )
		);

	}

	/**
	 * @testdox Given a product shortcode without params it should display message
	 */
	function test_product_without_params() {

		$this->assertEquals(
			do_shortcode( '[rstore_product]' ),
			'Post id is not valid.'
		);

	}

	/**
	 * @testdox Given a product shortcode with invalid post id it should display message
	 */
	function test_product_with_invalid_post_id() {

		$this->assertEquals(
			do_shortcode( '[rstore_product post_id=12]' ),
			'Post id is not valid.'
		);

	}

	/**
	 * @testdox Given a valid product shortcode it should generate html
	 */
	function test_product_with_valid_post_id() {

		$post = Tests\Helper::create_product( 'Test Product' );

		$this->assertRegExp(
			'/Test Product/',
			do_shortcode( '[rstore_product post_id=' . $post->ID . ']' )
		);

	}

	/**
	 * @testdox Given a valid product shortcode with all blank params it should generate nothing
	 */
	function test_product_with_all_blank_params() {

		$post = Tests\Helper::create_product( 'Another Product' );

		$content = '[rstore_product
      post_id=' . $post->ID . '
      show_title=0
      show_content=0
      show_price=0
      redirect = 0
      button_label=""
      text_cart=""
      image_size=none
      ]';

		do_shortcode( $content );

		$this->expectOutputString( '' );
	}

	/**
	 * @testdox Given a valid product shortcode with redirect=1 params it should render form post
	 */
	function test_product_with_redirect_1_param() {

		$post = Tests\Helper::create_product( 'Another Product good' );

		$content = '[rstore_product
      post_id=' . $post->ID . '
      redirect=1
      ]';

		$this->assertRegExp(
			'/<form class="rstore-add-to-cart-form"/',
			do_shortcode( $content )
		);

	}

	/**
	 * @testdox Given a valid product shortcode with redirect=0 params it should render div
	 */
	function test_product_with_redirect_0_param() {

		$post = Tests\Helper::create_product( 'Another Product good' );

		$content = '[rstore_product
      post_id=' . $post->ID . '
      redirect=0
      ]';

		$this->assertRegExp(
			'/<div class="rstore-add-to-cart-form"/',
			do_shortcode( $content )
		);

	}

	/**
	 * @testdox Given a valid login shortcode it should generate the login html
	 */
	function test_login() {

		$content = '[rstore_login
      welcome_message="aaaa"
      login_button_text="bbbb"
      logout_button_text="cccc"
      ]';

		$this->assertRegExp(
			'/<a class="logout-link" href="https:\/\/sso.secureserver.net\/logout\/" rel="nofollow">cccc<\/a>/',
			do_shortcode( $content )
		);

	}

	/**
	 * @testdox Given a domain shortcode it should generate html
	 */
	function test_domain() {

		rstore_update_option( 'pl_id', 12345 );

		$this->assertRegExp(
			'/<form role="search" method="get" class="search-form" action="https:\/\/www.secureserver.net\/products\/domain-registration\/find\/\?plid=12345">/',
			do_shortcode( '[rstore_domain]' )
		);

	}

	/**
	 * @testdox Given a domain transfer shortcode it should generate html
	 */
	function test_domain_transfer() {

		rstore_update_option( 'pl_id', 12345 );

		$this->assertRegExp(
			'/<form role="search" method="get" class="search-form" action="https:\/\/www.secureserver.net\/products\/domain-transfer\/\?plid=12345">/',
			do_shortcode( '[rstore_domain_transfer]' )
		);

	}

	/**
	 * @testdox Test invalid widget.
	 */
	function test_invalid_widget() {

		$this->assertFalse( rstore_is_widget() );

	}

	/**
	 * @testdox Test valid widget.
	 */
	function test_valid_widget() {

		$this->assertTrue(
			rstore_is_widget(
				[
					'widget_id' => 'widget-id-123',
				]
			)
		);

	}

	/**
	 * @testdox Given an icon shortcode it should generate html
	 */
	function test_icon_shortcode() {

		$this->assertRegExp(
			'/<title>Additional Products<\/title>/',
			do_shortcode( '[rstore_icon]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode with class it should generate html
	 */
	function test_icon_shortcode_class() {

		$this->assertRegExp(
			'/<div class=\"rstore-product-icons test-class\">/',
			do_shortcode( '[rstore_icon class="test-class"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode with product post it should generate html
	 */
	function test_icon_shortcode_post() {

		$post = Tests\Helper::create_product();

		$this->assertRegExp(
			'/<title>Additional Products<\/title>/',
			do_shortcode( '[rstore_icon post_id=' . $post->ID . ']' )
		);

	}

	/**
	 * @testdox Given an icon shortcode with invalid product post it should generate html
	 */
	function test_icon_shortcode_invalid_post() {

		$this->assertRegExp(
			'/Post id is not valid./',
			do_shortcode( '[rstore_icon post_id=12]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode domains it should generate html
	 */
	function test_icon_shortcode_domains() {

		$this->assertRegExp(
			'/<title>Domains<\/title>/',
			do_shortcode( '[rstore_icon icon="domains"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode email it should generate html
	 */
	function test_icon_shortcode_email() {

		$this->assertRegExp(
			'/<title>Email<\/title>/',
			do_shortcode( '[rstore_icon icon="email"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode hosting it should generate html
	 */
	function test_icon_shortcode_hosting() {

		$this->assertRegExp(
			'/<title>Hosting<\/title>/',
			do_shortcode( '[rstore_icon icon="hosting"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode WordPress it should generate html
	 */
	function test_icon_shortcode_wordpress() {

		$this->assertRegExp(
			'/<title>WordPress<\/title>/',
			do_shortcode( '[rstore_icon icon="wordpress"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode websites it should generate html
	 */
	function test_icon_shortcode_websites() {

		$this->assertRegExp(
			'/<title>Website Builder<\/title>/',
			do_shortcode( '[rstore_icon icon="websites"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode seo it should generate html
	 */
	function test_icon_shortcode_seo() {

		$this->assertRegExp(
			'/<title>Search Engine Visibility<\/title>/',
			do_shortcode( '[rstore_icon icon="seo"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode ssl it should generate html
	 */
	function test_icon_shortcode_ssl() {

		$this->assertRegExp(
			'/<title>SSL<\/title>/',
			do_shortcode( '[rstore_icon icon="ssl"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode email-marketing it should generate html
	 */
	function test_icon_shortcode_email_marketing() {

		$this->assertRegExp(
			'/<title>Email Marketing<\/title>/',
			do_shortcode( '[rstore_icon icon="email-marketing"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode website-security it should generate html
	 */
	function test_icon_shortcode_website_security() {

		$this->assertRegExp(
			'/<title>Website Security<\/title>/',
			do_shortcode( '[rstore_icon icon="website-security"]' )
		);

	}

	/**
	 * @testdox Given an icon shortcode dedicated-ip it should generate html
	 */
	function test_icon_shortcode_dedicated_ip() {

		$this->assertRegExp(
			'/<title>Dedicated IP<\/title>/',
			do_shortcode( '[rstore_icon icon="dedicated-ip"]' )
		);

	}


}
