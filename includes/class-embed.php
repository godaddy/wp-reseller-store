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
	 * Add styles to the embded <head>.
	 *
	 * @action embed_head
	 * @since  NEXT
	 */
	public function head() {

		if ( Post_Type::SLUG !== get_post_type() ) {

			return;

		}

		?>
		<style type="text/css">
		.wp-embed-excerpt p {
			margin: 0 0 1em;
		}
		.rstore-embed-price {
			display: block;
			margin-top: -0.75em;
			font-weight: 700;
			opacity: 0.75;
		}
		a.rstore-embed-button {
			display:inline-block;
			padding: 0.5em;
			border: 1px solid #ddd;
		}
		a.rstore-embed-button:hover, a.rstore-embed-button:focus {
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

		if ( Post_Type::SLUG !== get_post_type() ) {

			return;

		}

		global $post;

		ob_start();

		printf(
			'<p><span class="rstore-embed-price">%s</span></p>',
			'$8.99' // @TODO Pull from API
		);

		$redirect = ( 1 === (int) Plugin::get_product_meta( $post->ID, 'add_cart_redirect' ) );

		printf(
			'<p><a href="%s" class="rstore-embed-button" data-id="%s" data-redirect="%s">%s</a></p>',
			esc_url( add_query_arg( 'add_to_cart', 'true', get_permalink( $post->ID ) ) ),
			esc_attr( get_post_meta( $post->ID, 'rstore_id', true ) ),
			esc_attr( $redirect ? 'true' : 'false' ),
			esc_html( Plugin::get_product_meta( $post->ID, 'add_cart_button_label', esc_html__( 'Add to Cart', 'reseller-store' ) ) )
		);

		the_excerpt();

		return ob_get_clean();

	}

}
