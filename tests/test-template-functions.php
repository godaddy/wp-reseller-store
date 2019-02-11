<?php
/**
 * GoDaddy Reseller Store Template Functions tests
 */

namespace Reseller_Store;

final class TestTemplateFunctions extends TestCase {

	/**
	 * @testdox Given product it should echo the cart form.
	 */
	public function test_rstore_add_to_cart_form() {

		$post = Tests\Helper::create_product();

		rstore_add_to_cart_form( $post->ID, true );

		$this->expectOutputRegex( '/<div class="rstore-add-to-cart-form"><div><button class="rstore-add-to-cart button btn btn-primary" data-id="wordpress-basic" data-quantity="1" data-redirect="true">Add to cart<\/button><\/div><div class="rstore-loading rstore-loading-hidden"><\/div><div class="rstore-cart rstore-cart-hidden"><span class="dashicons dashicons-yes rstore-success"><\/span><a href="https:\/\/cart.secureserver.net\/go\/checkout\/"  rel="nofollow"><\/a><\/div><div class="rstore-message rstore-message-hidden"><\/div><\/div>/' );

	}

}
