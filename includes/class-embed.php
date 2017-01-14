<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Embed {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'embed_head', [ $this, 'head' ] );

		add_filter( 'the_excerpt_embed', [ $this, 'excerpt' ] );

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
	 * @return int|false  Returns the number cache entries deleted, `false` on error.
	 */
	public static function flush_cache() {

		global $wpdb;

		$results = $wpdb->query( "DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` LIKE '_oembed_%';" );

		// Every cache row has an expiration row, divide by two
		return is_int( $results ) ? $results / 2 : $results;

	}

	/**
	 * Add styles to the embed <head>.
	 *
	 * @action embed_head
	 * @global WP_Post $post
	 * @since  NEXT
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
		</style>
		<?php

	}

	/**
	 * Customize the embed excerpt.
	 *
	 * @filter the_excerpt_embed
	 * @global WP_Post $post
	 * @since  NEXT
	 *
	 * @param  string $excerpt
	 *
	 * @return string
	 */
	public function excerpt( $excerpt ) {

		global $post;

		if ( Post_Type::SLUG !== $post->post_type ) {

			return $excerpt;

		}

		$output  = wpautop( rstore_price( $post->ID, false ) );
		$output .= wpautop( rstore_add_to_cart_link( $post->ID, false ) );
		$output .= wpautop( $excerpt );

		return $output;

	}

}
