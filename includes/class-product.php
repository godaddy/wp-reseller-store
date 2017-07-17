<?php
/**
 * WP Reseller Store product class.
 *
 * Handles the Reseller Store products.
 *
 * @class    Reseller_Store/Product
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    NEXT
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
	public $product;

	/**
	 * Array of required properties and validation callbacks.
	 *
	 * @since 0.2.0
	 *
	 * @var array
	 */
	private $properties = [
		'id'         => 'strlen',
		'categories' => 'is_array',
		'image'      => 'strlen',
		'term'       => 'strlen',
		'listPrice'  => 'strlen',
		'title'      => 'strlen',
		'content'    => 'strlen',
	];

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param stdClass $product Product instance.
	 */
	public function __construct( $product ) {

		$this->product = json_decode( wp_json_encode( $product ) );

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

		return isset( $this->product->{$property} ) ? $this->product->{$property} : null;

	}

	/**
	 * Check if the product object is valid.
	 *
	 * @since 0.2.0
	 *
	 * @return bool  Returns `true` if the product object is valid, otherwise `false`.
	 */
	public function is_valid() {

		if ( ! is_a( $this->product, 'stdClass' ) ) {

			return false;

		}

		foreach ( $this->properties as $property => $validator ) {

			if (
				// The product must have the property.
				property_exists( $this->product, $property )
				&&
				// The property validator must be callable.
				is_callable( $validator )
				&&
				// The property value must return truthy when ran through the validator.
				$validator( $this->product->{$property} )
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
	public function exists() {

		$product_id = sanitize_title( $this->product->id ); // Product IDs are sanitized on import.

		$imported = (array) rstore_get_option( 'imported', [] );

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
	 * Check if an product image has already been imported.
	 *
	 * @global wpdb $wpdb
	 * @since  0.2.0
	 *
	 * @return int|false  Returns the attachment ID if it exists, otherwise `false`.
	 */
	public function image_exists() {

		$key = rstore_prefix( 'product_attachment_id-' . md5( $this->product->image ) );

		$attachment_id = (int) wp_cache_get( $key );

		if ( ! $attachment_id ) {

			global $wpdb;

			$attachment_id = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `ID` FROM `{$wpdb->posts}` as p LEFT JOIN `{$wpdb->postmeta}` as pm ON ( p.`ID` = pm.`post_id` ) WHERE p.`post_type` = 'attachment' AND pm.`meta_key` = %s AND pm.`meta_value` = %s;",
					rstore_prefix( 'image' ),
					esc_url_raw( $this->product->image ) // Image URLs are sanitized on import.
				)
			);

			wp_cache_set( $key, $attachment_id );

		}

		return ( $attachment_id > 0 ) ? $attachment_id : false;

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

		$import = new Import( $this, $post_id );

		return $import->result();

	}

}
