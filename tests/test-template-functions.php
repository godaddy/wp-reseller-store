<?php
/**
 * GoDaddy Reseller Store Template Functions tests
 */

namespace Reseller_Store;

final class TestTemplateFunctions extends TestCase {

	/**
	 * @testdox Given product it should echo the cart form.
	 */
	public function test_rstore_add_to_cart_form_echo_redirect() {

		rstore_update_option( 'pl_id', 1592 );

		$post = Tests\Helper::create_product();

		rstore_add_to_cart_form( $post, true );

		$this->expectOutputRegex( '/<form class="rstore-add-to-cart-form" method="POST" action="https:\/\/www.secureserver.net\/api\/v1\/cart\/1592\/\?redirect=1&plid=1592"><input type="hidden" name="items" value=\'\[{"id":"wordpress-basic","quantity":1}\]\' \/><button class="rstore-add-to-cart button btn btn-primary" type="submit">Add to cart<\/button><div class="rstore-loading rstore-loading-hidden"><\/div><\/form>/' );

	}

	public function test_rstore_add_to_cart_form_open_new_tab() {

		rstore_update_option( 'pl_id', 1592 );

		$post = Tests\Helper::create_product();

		rstore_add_to_cart_form( $post, true, null, true);

		$this->expectOutputRegex( '/<form class="rstore-add-to-cart-form" method="POST" action="https:\/\/www.secureserver.net\/api\/v1\/cart\/1592\/\?redirect=1&plid=1592" target="_blank"><input type="hidden" name="items" value=\'\[{"id":"wordpress-basic","quantity":1}\]\' \/><button class="rstore-add-to-cart button btn btn-primary" type="submit">Add to cart<\/button><\/form>/' );

	}

	/**
	 * @testdox Given product with no redirect it should echo the cart div.
	 */
	public function test_rstore_add_to_cart_form_echo_no_redirect() {

		$post = Tests\Helper::create_product();

		rstore_add_to_cart_form( $post, true, null, null, null, false );

		$this->expectOutputRegex( '/<div class="rstore-add-to-cart-form"><div><button class="rstore-add-to-cart button btn btn-primary" data-id="wordpress-basic" data-quantity="1">Add to cart<\/button><\/div><div class="rstore-loading rstore-loading-hidden"><\/div><div class="rstore-cart rstore-cart-hidden"><span class="dashicons dashicons-yes rstore-success"><\/span><a href="https:\/\/cart.secureserver.net\/go\/checkout\/"  rel="nofollow">Continue to cart<\/a><\/div><div class="rstore-message rstore-message-hidden"><\/div><\/div>/' );

	}

	/**
	 * @testdox Given domain product rstore_add_to_cart_form should return empty.
	 */
	public function test_rstore_add_to_cart_form_domain() {

		$post = Tests\Helper::create_product( 'Domain Registration', 'domain' );

		$content = rstore_add_to_cart_form( $post, true, null, null, false );

		$this->assertEmpty( $content );

	}

	/**
	 * @testdox Given product it should echo the product price.
	 */
	public function test_rstore_price_echo() {

		rstore_update_option( 'pl_id', 1592 );

		$post = Tests\Helper::create_product();

		rstore_price( $post, true );

		$this->expectOutputRegex( '/<div class="rstore-pricing"><span class="rstore-retail-price">\$70.00<\/span><span class="rstore-price rstore-has-sale-price">\$50.00<\/span> \/ per year<\/div>/' );

	}

	/**
	 * @testdox Given domain product rstore_price should return emtpy.
	 */
	public function test_rstore_price_domain() {

		rstore_update_option( 'pl_id', 1592 );

		$post = Tests\Helper::create_product( 'Domain Registration', 'domain' );

		$content = rstore_price( $post, true );

		$this->assertEmpty( $content );

	}


}
