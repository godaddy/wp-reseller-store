<?php
/**
 * GoDaddy Reseller Store Product tests
 */

namespace Reseller_Store;

final class TestProduct extends TestCase {

	/**
	 * Product fixture.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	private $fixture = '{
    "id": "wordpress-basic",
    "categories": [
      {
        "Hosting": [
          "WordPress"
        ]
      },
      "Websites"
    ],
    "tags": [
      "hosting",
      "WordPress",
      "websites"
    ],
    "title": "WordPress Basic",
    "content": "<p>Think basic sites and blogs and startups.</p>\n<ul>\n<li>1 website</li>\n<li>10GB SSD storage</li>\n<li>25,000 monthly visitors</li>\n<li>SFTP</li>\n</ul>\n",
    "term": "month",
    "image": "https://img1.wsimg.com/rcc/products/banner/46.png",
    "imageId": "46",
    "listPrice": "$7.99",
    "salePrice": false
  }';

	/**
	 * Setup.
	 */
	function setUp() {

		parent::setUp();

	}

	/**
	 * @testdox Test Import Class exist.
	 */
	function test_basics() {

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Product' ),
			'Class \Product is not found'
		);

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Import' ),
			'Class \Import is not found'
		);

	}

	/**
	 * @testdox Given a product json it should create the product
	 */
	public function test_new_product() {

		$product = new Product( json_decode( $this->fixture ) );

		$this->assertEquals( 'WordPress Basic', $product->title );
		$this->assertEquals( 'wordpress-basic', $product->id );

		$this->assertTrue( $product->is_valid() );

	}

	/**
	 * @testdox Given a product json it should create a post when importing
	 */
	public function test_product_import() {

		new Post_Type;
		new Taxonomy_Category;
		new Taxonomy_Tag;

		do_action( 'init' );

		$product = new Product( json_decode( $this->fixture ) );
		$result = $product->import();

		$this->assertTrue( $result );

	}

}
