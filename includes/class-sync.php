<?php
/**
 * GoDaddy Reseller Store sync class.
 *
 * Handles the Reseller Store syncing.
 *
 * @class    Reseller_Store/Sync
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Sync {

	/**
	 * The time to wait in between API syncs (in seconds).
	 *
	 * @since 0.2.0
	 *
	 * @var int
	 */
	private $ttl = 14400; // 4 hours

	/**
	 * The time to wait in between API sync retries (in seconds).
	 *
	 * @since 0.2.0
	 *
	 * @var int
	 */
	private $retry_ttl = 120;

	/**
	 * Array of product properties that should be synced.
	 *
	 * @since 0.2.0
	 *
	 * @var array
	 */
	private $properties = array( 'content', 'listPrice', 'salePrice', 'term', 'title' );

	/**
	 * Class constructor.
	 */
	public function __construct() {

		/**
		 * Filter the time to wait in between API syncs (in seconds).
		 *
		 * Default: 4 hours
		 *
		 * @since 0.2.0
		 *
		 * @var int
		 */
		$this->ttl = (int) apply_filters( 'rstore_sync_ttl', $this->ttl );

		/**
		 * Filter the time to wait in between API sync retries (in seconds).
		 *
		 * Default: 2 mins
		 *
		 * Instead of using the normal TTL after a sync failure, this TTL will
		 * be used so we can try again sooner. Set this value to something
		 * _equal-to or less-than_ than the normal TTL.
		 *
		 * Retries will always use the shortest TTL available, meaning, if the
		 * normal TTL is lower than the retry TTL, the normal TTL will be used
		 * between sync retries.
		 *
		 * @since 0.2.0
		 *
		 * @var int
		 */
		$this->retry_ttl = (int) apply_filters( 'rstore_sync_retry_ttl', $this->retry_ttl );

		// Always use the shortest TTL available for retries.
		$this->retry_ttl = ( $this->retry_ttl > $this->ttl ) ? $this->ttl : $this->retry_ttl;

		/**
		 * Filter the array of product properties that should be synced.
		 *
		 * Default: content, listPrice, salePrice, term, title
		 *
		 * @since 0.2.0
		 *
		 * @var array
		 */
		$this->properties = (array) apply_filters( 'rstore_sync_properties', $this->properties );

		// After the post type and taxonomies are registered.
		add_action( 'init', array( $this, 'check' ), 11 );

	}

	/**
	 * Check if we are due for a sync.
	 *
	 * @action init
	 * @since  0.2.0
	 */
	public function check() {

		if ( time() < (int) rstore_get_option( 'next_sync' ) ) {

			return;

		}

		// Use the retry TTL by default in case this sync returns `false`.
		rstore_update_option( 'next_sync', time() + $this->retry_ttl );

		$synced = $this->sync_product_meta();

		if ( $synced ) {

			rstore_update_option( 'last_sync', time() );

			rstore_update_option( 'next_sync', time() + $this->ttl );

		}

	}

	/**
	 * Sync product properties from the API to post meta.
	 *
	 * @since  0.2.0
	 *
	 * @return bool
	 */
	private function sync_product_meta() {

		$products = rstore_get_products( true );

		if ( is_wp_error( $products ) || ! $products ) {

			return false;

		}

		$imported = (array) rstore_get_option( 'imported', array() );

		foreach ( (array) $products as $product ) {

			$post_id = array_search( $product->id, $imported, true );

			if ( false === $post_id ) {

				continue;

			}

			$meta = array();

			foreach ( $this->properties as $property ) {

				if ( isset( $product->{$property} ) ) {

					$meta[ $property ] = $product->{$property};

				}
			}

			rstore_bulk_update_post_meta( $post_id, $meta );

		}

		return true;

	}

}
