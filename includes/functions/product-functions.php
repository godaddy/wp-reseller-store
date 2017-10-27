<?php
/**
 * GoDaddy Reseller Store product functions.
 *
 * Contains the Reseller Store product functions used throughout the plugin.
 *
 * @package  Reseller_Store/Plugin
 * @author   GoDaddy
 * @since    1.0.0
 */

/**
 * Check whether products exist.
 *
 * Ignores the `auto-draft` post status.
 *
 * Product count is cached in memory to prevent duplicate
 * queries on the same page load.
 *
 * @global wpdb $wpdb
 * @since  0.2.0
 *
 * @return bool  Returns `true` if there are product posts, otherwise `false`.
 */
function rstore_has_products() {

	$key = rstore_prefix( 'products_count' );

	$count = wp_cache_get( $key );

	if ( false === $count ) {

		global $wpdb;

		$count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM `{$wpdb->posts}` WHERE `post_type` = %s AND `post_status` != 'auto-draft';",
				Reseller_Store\Post_Type::SLUG
			)
		);

		wp_cache_set( $key, $count );

	}

	return ( $count > 0 );

}

/**
 * Clear the product count cache.
 *
 * Product count is cached in memory to prevent duplicate
 * queries on the same page load.
 *
 * @global wpdb $wpdb
 * @since  1.0.0
 *
 * @return bool  Returns `true` on successful removal, `false` on failure
 */
function rstore_clear_cache() {

	$key = rstore_prefix( 'products_count' );

	return wp_cache_delete( $key );
}

/**
 * Check if the site has imported all available products.
 *
 * @since 0.2.0
 *
 * @return bool  Returns `true` if all available products have been imported, otherwise `false`.
 */
function rstore_has_all_products() {

	return ! (bool) rstore_get_missing_products();

}

/**
 * Return an array of missing product IDs that can be imported.
 *
 * @since 0.2.0
 *
 * @return array  Returns an array of product IDs, otherwise an empty array.
 */
function rstore_get_missing_products() {

	if ( ! rstore_is_setup() ) {

		return [];

	}

	$products = rstore_get_products();

	if ( is_wp_error( $products ) || empty( $products[0]->id ) ) {

		return [];

	}

	$missing = array_diff(
		wp_list_pluck( $products, 'id' ),
		(array) rstore_get_option( 'imported', [] )
	);

	return ( $missing ) ? $missing : [];

}

/**
 * Return an array of products and cache them.
 *
 * @param  bool $hard (optional) Whether the transients should be deleted before fetching.
 *
 * @return array|WP_Error
 */
function rstore_get_products( $hard = false ) {

	if ( $hard ) {

		rstore_delete_transient( 'products' );

	}

	return rstore_get_transient(
		'products', [], function () {

			return rstore()->api->get( 'catalog/{pl_id}/products' );

		}
	);

}

/**
 * Return an array of products and cache them.
 *
 * @return array|WP_Error
 */
function rstore_get_demo_products() {

	return json_decode( file_get_contents( __DIR__ . '/demo.json' ), true ); // @codingStandardsIgnoreLine

}

/**
 * Return a product object from catalog.
 *
 * @param  string $product_id Product ID.
 *
 * @return stdClass|WP_Error
 */
function rstore_get_product( $product_id ) {

	$response = rstore()->api->get( 'catalog/{pl_id}/products/' . $product_id );

	$product = new Reseller_Store\Product( $response );

	if ( $product->is_valid() && $product->id === $product_id ) {

		return $product;

	}

	return new WP_Error(
		'product_not_found',
		/* translators: product name */
		esc_html__( 'Error: `%s` does not exist.', 'reseller-store' ),
		$product_id
	);

}

/**
 * Return a product meta value, or its global setting fallback.
 *
 * @since 0.2.0
 *
 * @param  int    $post_id         Product post ID.
 * @param  string $key             Product meta key.
 * @param  mixed  $default         (optional) Default meta value.
 * @param  bool   $option_fallback (optional) Fallback value.
 *
 * @return mixed
 */
function rstore_get_product_meta( $post_id, $key, $default = false, $option_fallback = false ) {

	$key = rstore_prefix( $key );

	$meta = get_post_meta( $post_id, $key, true );

	return ( $meta ) ? $meta : ( $option_fallback ? get_option( $key, $default ) : $default );

}
