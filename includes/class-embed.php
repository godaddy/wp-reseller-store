<?php
/**
 * GoDaddy Reseller Store embed class.
 *
 * Handles the GoDaddy Reseller Store database functionality and excerpt generation.
 *
 * @class    Reseller_Store/Embed
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Embed {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		add_action( 'embed_head', array( $this, 'head' ) );

		add_filter( 'the_excerpt_embed', array( $this, 'excerpt' ) );

	}

	/**
	 * Search and replace text across all post content.
	 *
	 * When product permalinks are changed, we want to update references to the
	 * old product base URL with the base URL. This will prevent broken links
	 * and 404 errors on product oEmbeds.
	 *
	 * - Excludes posts in the `revision` post type.
	 * - Excludes posts with an `auto-draft` post status.
	 * - Excludes posts with serialized data in the post content.
	 *
	 * @global wpdb $wpdb
	 * @since  0.2.0
	 *
	 * @param  string $search  The term to search.
	 * @param  string $replace The term to replace our $search term with.
	 *
	 * @return int|false Returns the number of posts updated, `false` on error.
	 */
	public static function search_replace_post_content( $search, $replace ) {

		global $wpdb;

		$results = $wpdb->query(
			$wpdb->prepare(
				"UPDATE `{$wpdb->posts}` SET `post_content` = REPLACE( `post_content`, %s, %s ) WHERE `post_type` != 'revision' AND `post_status` != 'auto-draft' AND `post_content` LIKE %s AND `post_content` NOT RLIKE '(a:[0-9]+:{)|(s:[0-9]+:)|(i:[0-9]+;)|(O:[0-9]+:\")';",
				$search,
				$replace,
				'%' . $search . '%'
			)
		);

		return is_int( $results ) ? $results : false;

	}

	/**
	 * Flush all oEmbed post meta cache.
	 *
	 * Note: This function may return `false`, or it may return `0` (which evaluates
	 * to `false`). Use the identical comparison operator (===) when relying on the
	 * return value of this method.
	 *
	 * @global wpdb $wpdb
	 *
	 * @return int|false Returns the number of cache entries deleted, `false` on error.
	 */
	public static function flush_cache() {

		global $wpdb;

		$results = $wpdb->query( "DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` LIKE '_oembed_%';" );

		// Every cache row has an expiration row, divide by two.
		return is_int( $results ) ? $results / 2 : $results;

	}

	/**
	 * Add styles to the embed <head>.
	 *
	 * @action embed_head
	 * @global WP_Post $post
	 * @since  0.2.0
	 */
	public function head() {

		global $post;

		if ( Post_Type::SLUG !== $post->post_type ) {

			return;

		}

		?>
		<style type="text/css">
		.wp-embed-excerpt p {
			margin: 0 0 1em;
		}
		.wp-embed-excerpt .rstore-pricing {
			font-size: 0.9em;
		}
		.wp-embed-excerpt .rstore-price {
			font-size: 1rem;
			font-weight: bold;
			color: #41a62a;
		}
		.wp-embed-excerpt .rstore-price del {
			color: #ccc;
		}
		.wp-embed-excerpt .rstore-add-to-cart {
			display:inline-block;
			padding: 0.5em;
			border: 1px solid #ddd;
		}
		.wp-embed-excerpt .rstore-add-to-cart:hover,
		.wp-embed-excerpt .rstore-add-to-cart:focus {
			text-decoration: none;
			color: #999;
			border: 1px solid #ccc;
		}
		.wp-embed
		{
			border:none;
			color: #000;
		}
		.wp-embed-footer
		{
			display:none;
		}
		</style>
		<base target="_parent">
		<?php

	}

	/**
	 * Customize the embed excerpt.
	 *
	 * @filter the_excerpt_embed
	 * @global WP_Post $post
	 * @since  0.2.0
	 *
	 * @param  string $excerpt The original excerpt.
	 *
	 * @return string
	 */
	public function excerpt( $excerpt ) {

		global $post, $wp_current_filter;

		if ( Post_Type::SLUG !== $post->post_type ) {

			return $excerpt;

		}

		if ( in_array( 'the_content', $wp_current_filter, true ) ) {

			$output = wpautop( get_post_field( 'post_content', $post->ID ) );

		} else {

			$output = wpautop( apply_filters( 'the_content', get_post_field( 'post_content', $post->ID ) ) );
		}

		return $output;

	}

}
