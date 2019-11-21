<?php
/**
 * GoDaddy Reseller Store product class.
 *
 * Handles the Reseller Store products.
 *
 * @class    Reseller_Store/Product
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Product {

	/**
	 * Product object.
	 *
	 * @since 0.2.0
	 *
	 * @var stdClass
	 */
	public $fields;

	/**
	 * Array of required properties and validation callbacks.
	 *
	 * @since 0.2.0
	 *
	 * @var array
	 */
	private $properties = array(
		'id'         => 'strlen',
		'categories' => 'is_array',
		'tags'       => 'is_array',
		'image'      => 'strlen',
		'term'       => 'strlen',
		'listPrice'  => 'strlen',
		'title'      => 'strlen',
		'content'    => 'strlen',
	);

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param stdClass $product Product instance.
	 */
	public function __construct( $product ) {

		$this->fields = json_decode( wp_json_encode( $product ) );

	}

	/**
	 * Return a product property.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $property Property name.
	 *
	 * @return mixed|null
	 */
	public function __get( $property ) {

		return isset( $this->fields->{$property} ) ? $this->fields->{$property} : null;

	}

	/**
	 * Check if the product object is valid.
	 *
	 * @since 0.2.0
	 *
	 * @return bool  Returns `true` if the product object is valid, otherwise `false`.
	 */
	public function is_valid() {

		if ( ! is_a( $this->fields, 'stdClass' ) ) {

			return false;

		}

		foreach ( $this->properties as $property => $validator ) {

			if (
				// The product must have the property.
				property_exists( $this->fields, $property )
				&&
				// The property validator must be callable.
				is_callable( $validator )
				&&
				// The property value must return truthy when ran through the validator.
				$validator( $this->fields->{$property} )
			) {

				return true;

			}
		}

		return false;

	}

	/**
	 * Check if a product has already been imported.
	 *
	 * @global wpdb $wpdb
	 * @since  0.2.0
	 *
	 * @return int|false  Returns the post ID if it exists, otherwise `false`.
	 */
	private function exists() {

		$product_id = sanitize_title( $this->fields->id ); // Product IDs are sanitized on import.

		$imported = (array) rstore_get_option( 'imported', array() );

		if ( $imported ) {

			return array_search( $product_id, $imported, true );

		}

		// Query post meta if the imported option is missing.
		global $wpdb;

		$post_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `ID` FROM `{$wpdb->posts}` as p LEFT JOIN `{$wpdb->postmeta}` as pm ON ( p.`ID` = pm.`post_id` ) WHERE p.`post_type` = %s AND pm.`meta_key` = %s AND pm.`meta_value` = %s;",
				Post_Type::SLUG,
				rstore_prefix( 'id' ),
				$product_id // Already sanitized.
			)
		);

		return ( $post_id > 0 ) ? $post_id : false;

	}

	/**
	 * Import the product.
	 *
	 * @since 0.2.0
	 *
	 * @param  int $post_id Product post ID.
	 *
	 * @return true|WP_Error  Returns `true` on success, `WP_Error` on failure.
	 */
	public function import( $post_id = 0 ) {

		if ( ! $this->exists() ) {
			$import = new Import( $this, $post_id );
			return $import->import_product();
		} else {
			return false;
		}
	}
}
