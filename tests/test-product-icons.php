<?php
/**
 * GoDaddy Reseller Store Product SVG Icons tests
 */

namespace Reseller_Store;

final class TestProductIcons extends TestCase {


	/**
	 * @testdox Test that Product_Icons class exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Product_Icons' ) );

	}

	/**
	 * @testdox Given an image_id it should generate html
	 */
	function test_get_icon() {

		$this->assertRegExp(
			'/<title>Additional Products<\/title>/',
			Product_Icons::get_icon( 'default' )
		);

	}

	/**
	 * @testdox Given an image_id with class it should generate html
	 */
	function test_get_icon_css_class() {

		$this->assertRegExp(
			'/<div class=\"rstore-product-icons test-class\">/',
			Product_Icons::get_icon( 'default', 'test-class' )
		);

	}

	/**
	 * @testdox Given rstore_icon_html filter it should return filter html
	 */
	function test_rstore_icon_html_filter() {

		add_filter(
			'rstore_icon_html',
			function( $content, $image_id, $class_name ) {
				return "filter {$image_id} {$class_name}";
			},
			10,
			3
		);

		$this->assertEquals(
			'filter default test-class',
			Product_Icons::get_icon( 'default', 'test-class' )
		);

	}

	/**
	 * @testdox Given an image_id domains it should generate html
	 */
	function test_get_icon_domains() {

		$this->assertRegExp(
			'/<title>Domains<\/title>/',
			Product_Icons::get_icon( 'domains' )
		);

	}

	/**
	 * @testdox Given an image_id email it should generate html
	 */
	function test_get_icon_email() {

		$this->assertRegExp(
			'/<title>Email<\/title>/',
			Product_Icons::get_icon( 'email' )
		);

	}

	/**
	 * @testdox Given an image_id hosting it should generate html
	 */
	function test_get_icon_hosting() {

		$this->assertRegExp(
			'/<title>Hosting<\/title>/',
			Product_Icons::get_icon( 'hosting' )
		);

	}

	/**
	 * @testdox Given an image_id WordPress it should generate html
	 */
	function test_get_icon_wordpress() {

		$this->assertRegExp(
			'/<title>WordPress<\/title>/',
			Product_Icons::get_icon( 'WordPress' )
		);

	}

	/**
	 * @testdox Given an image_id websites it should generate html
	 */
	function test_get_icon_websites() {

		$this->assertRegExp(
			'/<title>Websites<\/title>/',
			Product_Icons::get_icon( 'websites' )
		);

	}

	/**
	 * @testdox Given an image_id seo it should generate html
	 */
	function test_get_icon_seo() {

		$this->assertRegExp(
			'/<title>Search Engine Visibility<\/title>/',
			Product_Icons::get_icon( 'seo' )
		);

	}

	/**
	 * @testdox Given an image_id ssl it should generate html
	 */
	function test_get_icon_ssl() {

		$this->assertRegExp(
			'/<title>SSL<\/title>/',
			Product_Icons::get_icon( 'ssl' )
		);

	}

	/**
	 * @testdox Given an image_id email-marketing it should generate html
	 */
	function test_get_icon_email_marketing() {

		$this->assertRegExp(
			'/<title>Email Marketing<\/title>/',
			Product_Icons::get_icon( 'email-marketing' )
		);

	}

	/**
	 * @testdox Given an image_id website-security it should generate html
	 */
	function test_get_icon_website_security() {

		$this->assertRegExp(
			'/<title>Website Security<\/title>/',
			Product_Icons::get_icon( 'website-security' )
		);

	}

	/**
	 * @testdox Given an image_id dedicated-ip it should generate html
	 */
	function test_get_icon_dedicated_ip() {

		$this->assertRegExp(
			'/<title>Dedicated IP<\/title>/',
			Product_Icons::get_icon( 'dedicated-ip' )
		);

	}

	/**
	 * @testdox Given a product post it should generate html
	 */
	function test_icon_product_id() {

		$post = Tests\Helper::create_product();

		$this->assertRegExp(
			'/<title>Additional Products<\/title>/',
			Product_Icons::get_product_icon( $post )
		);

	}
}
