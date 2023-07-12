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
 * Retrieve reseller products.
 *
 * @return array Reseller product posts.
 * @since 1.6.0
 */
function rstore_get_product_list() {

	$query = new \WP_Query(
		array(
			'post_type'   => \Reseller_Store\Post_Type::SLUG,
			'post_status' => 'publish',
			'nopaging'    => true,
		)
	);

	$products = array();
	foreach ( $query->posts as $post ) {
		$products[ strval( $post->ID ) ] = esc_html( get_the_title( $post->ID ) );
	}

	return $products;
}

/**
 * Clear the product count cache.
 *
 * Product count is cached in memory to prevent duplicate
 * queries on the same page load.
 *
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

		return array();

	}

	$products = rstore_get_products();

	if ( is_wp_error( $products ) || empty( $products[0]->id ) ) {

		return array();

	}

	$missing = array_diff(
		wp_list_pluck( $products, 'id' ),
		(array) rstore_get_option( 'imported', array() )
	);

	return ( $missing ) ? $missing : array();

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
		'products',
		array(),
		function () {

			return rstore()->api->get( 'catalog/{pl_id}/products' );

		}
	);

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
 *
 * @return mixed
 */
function rstore_get_product_meta( $post_id, $key ) {

	$key = rstore_prefix( $key );

	$meta = get_post_meta( $post_id, $key, true );

	return $meta;

}

/**
 * Returns true when viewing a reseller store product.
 *
 * @since  1.6.0
 *
 * @param object $post  The post of to check type on.
 *
 * @return bool True is if post_type is reseller_store
 */
function rstore_is_product( $post ) {
	return ( \Reseller_Store\Post_Type::SLUG === $post->post_type );
}
add_filter( 'rstore_is_product', 'rstore_is_product' );



/**
 * Checks if the shortcode is being rendered as a widget.
 *
 * @since 1.6.0
 *
 * @param array $atts Shortcode attributes.
 *
 * @return boolean    True is widget_id key is set, else false.
 */
function rstore_is_widget( $atts = array() ) {

	return isset( $atts['widget_id'] );

}
add_filter( 'rstore_is_widget', 'rstore_is_widget' );

