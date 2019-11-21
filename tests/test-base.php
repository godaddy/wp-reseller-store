<?php
/**
 * GoDaddy Reseller Store Base Tests
 */

namespace Reseller_Store;

final class TestBase extends TestCase {

	/**
	 * Setup.
	 */
	function setUp() {

		parent::setUp();

	}

	/**
	 * Test the plugin data exists.
	 */
	public function test_plugin_data() {

		$static_attributes = array(
			'version',
			'basename',
			'base_dir',
			'assets_url',
			'api',
		);

		array_map(
			function( $attr ) {

					$this->assertNotNull( rstore()->$attr );

			},
			$static_attributes
		);

	}

	/**
	 * Test the required classes are instantiated.
	 */
	public function test_classes_exist() {

		$classes = array(
			'ButterBean',
			'Display',
			'Embed',
			'Permalinks',
			'Post_Type',
			'Sync',
			'Taxonomy_Category',
			'Taxonomy_Tag',
			'Widgets',
		);

		array_map(
			function( $class ) {

					$this->assertTrue( class_exists( __NAMESPACE__ . '\\' . $class ) );

			},
			$classes
		);

	}

}
