<?php
/**
 * GoDaddy Reseller Store Product tests
 */

namespace Reseller_Store;

final class TestProduct extends TestCase {

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

		$product = new Product( json_decode( Tests\Helper::$fixture ) );

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

		$product = new Product( json_decode( Tests\Helper::$fixture ) );
		$result = $product->import();

		$this->assertTrue( $result );

	}

}
