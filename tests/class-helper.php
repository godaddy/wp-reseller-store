<?php
/**
 * Class Helper.
 *
 * Helper class to create and test reseller_product post easily.
 */

namespace Reseller_Store\Tests;

final class Helper {

	/**
	 * Create a product post.
	 *
	 * @since NEXT
	 */
	public static function create_product_post() {
		$post_id = wp_insert_post( array(
			'post_title'    => 'WordPress Hosting',
			'post_name'     => 'wordpress-hosting',
			'post_type'     => 'reseller_product',
			'post_status'   => 'publish',
		) );

		$meta = array(
		'rstore_id'                         => 'wordpress_hosting',
		'rstore_categories'                 => [],
		'rstore_image'               => 'http://image',
		'rstore_term'                => 'year',
		'rstore_listPrice'               => '$70.00',
		'rstore_title'           => 'Wordpres Hosting',
		'rstore_content'                 => 'blah blah',
		'rstore_salePrice'                 => '$50.00',
		);
		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
		return get_post( $post_id );
	}

}
