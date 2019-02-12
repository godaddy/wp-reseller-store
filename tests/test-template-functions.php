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

		$post = Tests\Helper::create_product();

		rstore_add_to_cart_form( $post, true );

		$this->expectOutputRegex( '/<form class="rstore-add-to-cart-form" method="POST" action="https:\/\/www.secureserver.net\/api\/v1\/cart\/0\/\?redirect=1" ><input type="hidden" name="items" value=\'\[{"id":"wordpress-basic","quantity":1}\]\' \/><button class="rstore-add-to-cart button btn btn-primary" type="submit">Add to cart<\/button><div class="rstore-loading rstore-loading-hidden"><\/div><\/form>/' );

	}

	/**
	 * @testdox Given product with no redirect it should echo the cart div.
	 */
	public function test_rstore_add_to_cart_form_echo_no_redirect() {

		$post = Tests\Helper::create_product();

		rstore_add_to_cart_form( $post, true, null, null, false );

		$this->expectOutputRegex( '/<div class="rstore-add-to-cart-form"><div><button class="rstore-add-to-cart button btn btn-primary" data-id="wordpress-basic" data-quantity="1">Add to cart<\/button><\/div><div class="rstore-loading rstore-loading-hidden"><\/div><div class="rstore-cart rstore-cart-hidden"><span class="dashicons dashicons-yes rstore-success"><\/span><a href="https:\/\/cart.secureserver.net\/go\/checkout\/"  rel="nofollow">Continue to cart<\/a><\/div><div class="rstore-message rstore-message-hidden"><\/div><\/div>/' );

	}


}
