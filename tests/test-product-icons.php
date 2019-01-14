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
	 * @testdox Given an image_id domain-registration it should generate html
	 */
	function test_get_icon_domain_registration() {

		$this->assertRegExp(
			'/<title>Domain Registration<\/title>/',
			Product_Icons::get_icon( 'domain-registration' )
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
			'/<title>Website Builder<\/title>/',
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
	 * @testdox Given an image_id dedicated-server it should generate html
	 */
	function test_get_icon_dedicated_server() {

		$this->assertRegExp(
			'/<title>Dedicated Server<\/title>/',
			Product_Icons::get_icon( 'dedicated-server' )
		);

	}

	/**
	 * @testdox Given an image_id domain-transfer it should generate html
	 */
	function test_get_icon_domain_transfer() {

		$this->assertRegExp(
			'/<title>Domain Transfer<\/title>/',
			Product_Icons::get_icon( 'domain-transfer' )
		);

	}

	/**
	 * @testdox Given an image_id domain-backorder it should generate html
	 */
	function test_get_icon_domain_backorder() {

		$this->assertRegExp(
			'/<title>Domain Backorder<\/title>/',
			Product_Icons::get_icon( 'domain-backorder' )
		);

	}

	/**
	 * @testdox Given an image_id hosting-cpanel it should generate html
	 */
	function test_get_icon_hosting_cpanel() {

		$this->assertRegExp(
			'/<title>cPanel Hosting<\/title>/',
			Product_Icons::get_icon( 'hosting-cpanel' )
		);

	}

	/**
	 * @testdox Given an image_id hosting-windows it should generate html
	 */
	function test_get_icon_hosting_windows() {

		$this->assertRegExp(
			'/<title>Windows Hosting<\/title>/',
			Product_Icons::get_icon( 'hosting-windows' )
		);

	}

	/**
	 * @testdox Given an image_id online-calendar it should generate html
	 */
	function test_get_icon_online_calendar() {

		$this->assertRegExp(
			'/<title>Online Calendar<\/title>/',
			Product_Icons::get_icon( 'online-calendar' )
		);

	}

	/**
	 * @testdox Given an image_id online-storage it should generate html
	 */
	function test_get_icon_online_storage() {

		$this->assertRegExp(
			'/<title>Online Storage<\/title>/',
			Product_Icons::get_icon( 'online-storage' )
		);

	}

	/**
	 * @testdox Given an image_id online-store it should generate html
	 */
	function test_get_icon_online_store() {

		$this->assertRegExp(
			'/<title>Online Store<\/title>/',
			Product_Icons::get_icon( 'online-store' )
		);

	}

	/**
	 * @testdox Given an image_id reseller it should generate html
	 */
	function test_get_icon_reseller() {

		$this->assertRegExp(
			'/<title>Reseller<\/title>/',
			Product_Icons::get_icon( 'reseller' )
		);

	}

	/**
	 * @testdox Given an image_id sale it should generate html
	 */
	function test_get_icon_sale() {

		$this->assertRegExp(
			'/<title>On Sale<\/title>/',
			Product_Icons::get_icon( 'sale' )
		);

	}

	/**
	 * @testdox Given an image_id website-backup it should generate html
	 */
	function test_get_icon_website_backup() {

		$this->assertRegExp(
			'/<title>Website Backup<\/title>/',
			Product_Icons::get_icon( 'website-backup' )
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
