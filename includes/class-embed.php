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

		$output  = wpautop( Display::price( $post->ID, false ) );
		$output .= wpautop( Display::add_to_cart_link( $post->ID, false ) );
		$output .= wpautop( $excerpt );

		return $output;

	}

}
