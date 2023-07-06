<?php
/**
 * GoDaddy Reseller Store template functions.
 *
 * Contains the Reseller Store template functions used to display product data.
 *
 * @package  Reseller_Store/Plugin
 * @author   GoDaddy
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

/**
 * Display the price for a given product.
 *
 * @since 0.2.0
 *
 * @param  int|WP_Post|null $post (optional) Product WP_Post instance. Defaults to global $post.
 * @param  bool             $echo (optional) Echo the text.
 *
 * @return string|null
 */
function rstore_price( $post = null, $echo = false ) {

	$post = get_post( $post );

	$id = rstore_get_product_meta( $post->ID, 'id' );

	if ( 'domain' === $id ) {

		return;

	}

	$list = rstore_get_product_meta( $post->ID, 'listPrice' );

	$output = sprintf(
		'<span class="rstore-price">%s</span>',
		esc_html( $list )
	);

	$sale = rstore_get_product_meta( $post->ID, 'salePrice' );

	if ( $sale ) {

		$output = sprintf(
			'<span class="rstore-retail-price">%s</span><span class="rstore-price rstore-has-sale-price">%s</span>',
			esc_html( $list ),
			esc_html( $sale )
		);

	}

	$term = rstore_get_product_meta( $post->ID, 'term' );

	if ( $term ) {

		$output = sprintf(
			/* translators: 1. price, 2. subscription term - e.g. $10 / per month */
			esc_html_x( '%1$s / per %2$s', 'product price', 'reseller-store' ),
			$output,
			$term // xss ok.
		);

	}

	$output = sprintf( '<div class="rstore-pricing">%s</div>', $output );

	if ( $echo ) {

		echo $output; // xss ok.

	}

	return $output;

}

/**
 * Display an `Add to cart` form for a given product.
 *
 * @since 0.2.0
 *
 * @param  int|WP_Post|null $post Product WP_Post instance.
 * @param  bool             $echo (optional) Echo the text.
 * @param  string           $button_label (optional) Text to display in the button.
 * @param  string           $text_cart (optional) Text to display in the cart link.
 * @param  bool             $redirect (optional) Redirect to cart after adding item.
 *
 * @return string|null
 */
function rstore_add_to_cart_form( $post, $echo = false, $button_label = null, $button_new_tab = null, $text_cart = null, $redirect = true ) {

	$post = get_post( $post );

	$id = rstore_get_product_meta( $post->ID, 'id' );

	if ( 'domain' === $id ) {

		return;

	}

	$data = array(
		'id'       => $id,
		'quantity' => 1, // @TODO Future release.
	);

	if ( empty( $button_label ) ) {

		$button_label = rstore_get_product_meta( $post->ID, 'add_to_cart_button_label' );

		if ( empty( $button_label ) ) {

			$button_label = esc_html__( 'Add to cart', 'reseller-store' );

		}
	}

	if ( $redirect ) {

		$args['redirect'] = true;

		$cart_url = esc_url_raw( rstore()->api->url( 'cart_api', '', $args ) );

		$items = json_encode( array( $data ) );

		$cart_form = sprintf(
			'<form class="rstore-add-to-cart-form" method="POST" action="%s"%s><input type="hidden" name="items" value=\'%s\' /><button class="rstore-add-to-cart button btn btn-primary" type="submit">%s</button>%s</form>',
			$cart_url,
			$button_new_tab ? ' target="_blank"' : '',
			$items,
			esc_html( $button_label ),
			$button_new_tab ? '' : '<div class="rstore-loading rstore-loading-hidden"></div>'
		);

	} else {

		if ( empty( $text_cart ) ) {

			$text_cart = rstore_get_product_meta( $post->ID, 'cart_link_text' );

			if ( empty( $text_cart ) ) {

				$text_cart = esc_html__( 'Continue to cart', 'reseller-store' );

			}
		}

		$cart_link = sprintf(
			'<span class="dashicons dashicons-yes rstore-success"></span><a href="%s"  rel="nofollow">%s</a>',
			esc_url_raw( rstore()->api->url( 'cart' ), 'https' ),
			esc_html( $text_cart )
		);

		$button = rstore_add_to_cart_button( $data, $button_label );

		$cart_form = sprintf(
			'<div class="rstore-add-to-cart-form">%s<div class="rstore-loading rstore-loading-hidden"></div><div class="rstore-cart rstore-cart-hidden">%s</div><div class="rstore-message rstore-message-hidden"></div></div>',
			$button,
			$cart_link
		);

	}

	if ( $echo ) {

		echo $cart_form; // xss ok.

	}

	return $cart_form;

}

/**
 * Append an `Add to cart` form the end of product post content.
 *
 * @action the_content
 * @global WP_Post $post
 * @since  0.2.0
 *
 * @param  string $content Product content.
 *
 * @return string
 */
function rstore_append_add_to_cart_form( $content ) {

	global $post;

	if ( ! isset( $post ) ) {
		return $content;
	}

	if ( property_exists( $post, 'rstore_widget' ) && true === $post->rstore_widget ) {

		return $content;

	}

	$is_rest_request = ( defined( 'REST_REQUEST' ) && REST_REQUEST );

	if ( \Reseller_Store\Post_Type::SLUG === $post->post_type && ! is_feed() && ! $is_rest_request ) {

		$content .= rstore_price( $post->ID );

		$redirect = ! ( (bool) rstore_get_product_meta( $post->ID, 'skip_cart_redirect' ) );
		$content .= rstore_add_to_cart_form( $post->ID, false, null, null, null, $redirect );

	}

	return $content;

}

/**
 * Display an `Add to cart` button for a given product.
 *
 * @since 0.2.0
 *
 * @param  array  $cart_vars (required) Default cart values for product.
 * @param  string $button_label (optional) Text to display in the button.
 *
 * @return string|null
 */
function rstore_add_to_cart_button( $cart_vars, $button_label ) {

	$cart_vars = apply_filters( 'rstore_cart_options', $cart_vars );

	if ( ! is_array( $cart_vars ) ) {

		return;

	}

	$output = '<div><button class="rstore-add-to-cart button btn btn-primary"';

	foreach ( $cart_vars as $key => $data ) {

		$output .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $data ) . '"';

	}

	$output .= '>' . esc_html( $button_label ) . '</button></div>';

	return $output;

}
