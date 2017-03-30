<?php

/**
 * Check whether products exist.
 *
 * Ignores the `auto-draft` post status.
 *
 * Product count is cached in memory to prevent duplicate
 * queries on the same page load.
 *
 * @global wpdb $wpdb
 * @since  NEXT
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
 * Check if the site has imported all available products.
 *
 * @since NEXT
 *
 * @return bool  Returns `true` if all available products have been imported, otherwise `false`.
 */
function rstore_has_all_products() {

	return ! (bool) rstore_get_missing_products();

}

/**
 * Return an array of missing product IDs that can be imported.
 *
 * @since NEXT
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
 * @param  bool $hard (optional)
 *
 * @return array|WP_Error
 */
function rstore_get_products( $hard = false ) {

	if ( $hard ) {

		rstore_delete_transient( 'products' );

	}

	return rstore_get_transient( 'products', [], function () {

		return rstore()->api->get( 'catalog/{pl_id}/products' );

	} );

}

/**
 * Return a product object.
 *
 * @param  string $product_id
 * @param  bool   $hard (optional)
 *
 * @return stdClass|WP_Error
 */
function rstore_get_product( $product_id, $hard = false ) {

	foreach ( rstore_get_products( $hard ) as $product ) {

		$product = new Reseller_Store\Product( $product );

		if ( $product->is_valid() && $product->id === $product_id ) {

			return $product;

		}

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
 * @since NEXT
 *
 * @param  int    $post_id
 * @param  string $key
 * @param  mixed  $default         (optional)
 * @param  bool   $option_fallback (optional)
 *
 * @return mixed
 */
function rstore_get_product_meta( $post_id, $key, $default = false, $option_fallback = false ) {

	$key = rstore_prefix( $key );

	$meta = get_post_meta( $post_id, $key, true );

	return ( $meta ) ? $meta : ( $option_fallback ? get_option( $key, $default ) : $default );

}
