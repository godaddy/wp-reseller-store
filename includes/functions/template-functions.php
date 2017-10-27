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

/**
 * Display the price for a given product.
 *
 * @since 0.2.0
 *
 * @param  int|WP_Post|null $post (optional) Product WP_Post instance.
 * @param  bool             $echo (optional) Whether or not the value should be echoed.
 *
 * @return string|null
 */
function rstore_price( $post = null, $echo = true ) {

	$post = get_post( $post );

	$id = rstore_get_product_meta( $post->ID, 'id' );

	if ( 'domain' === $id ) {

		return;

	}

	$list = rstore_get_product_meta( $post->ID, 'listPrice' );

	if ( ! $list ) {

		return;

	}

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

	$output = sprintf( '<p class="rstore-pricing">%s</p>', $output );

	if ( ! $echo ) {

		return $output;

	}

	echo $output; // xss ok.

}

/**
 * Display an `Add to cart` form for a given product.
 *
 * @since 0.2.0
 *
 * @param  int|WP_Post|null $post Product WP_Post instance.
 * @param  bool             $echo Whether or not the form should be echoed.
 * @param  string           $button_label Text to display in the button.
 * @param  string           $text_cart Text to display in the cart link.
 * @param  bool             $redirect Redirect to cart after adding item.
 *
 * @return string|null
 */
function rstore_add_to_cart_form( $post, $echo, $button_label, $text_cart, $redirect ) {

	$cart_link = sprintf(
		'<span class="dashicons dashicons-yes rstore-success"></span><a href="%s"  rel="nofollow">%s</a>',
		rstore()->api->urls['cart'],
		esc_html( $text_cart )
	);

	ob_start();

	?>
	<div class="rstore-add-to-cart-form">
		<?php	rstore_add_to_cart_button( $post, $button_label, $redirect ); ?>
		<div class="rstore-loading rstore-loading-hidden" ></div>
		<div class="rstore-cart rstore-cart-hidden" ><?php echo $cart_link; ?></div>
		<div class="rstore-message"></div>
	</div>
	<?php

	$output = ob_get_clean();

	if ( ! $echo ) {

		return $output;

	}

	echo $output; // xss ok.

}

/**
 * Display an `Add to cart` button for a given product.
 *
 * @since 0.2.0
 *
 * @param  int|WP_Post|null $post (required) Product WP_Post instance.
 * @param  string           $param_label (optional) Text to display in the button.
 * @param  bool             $param_redirect (optional) Redirect to cart after adding item.
 * @param  bool             $echo (optional) Whether or not the add to cart button should be echoed.
 *
 * @return string|null
 */
function rstore_add_to_cart_button( $post, $param_label = null, $param_redirect = null, $echo = true ) {

	list( $id, $quantity, $redirect, $label ) = array_values( rstore_get_add_to_cart_vars( $post ) );

	if ( ! empty( $param_label ) ) {
		$label = $param_label;
	}

	if ( ! empty( $param_redirect ) ) {
		$redirect = $param_redirect;
	}

	if ( empty( $id ) || empty( $quantity ) || ! isset( $redirect ) || empty( $label ) ) {

		return;

	}

	if ( 'domain' === $id ) {

		return;

	}

	$output = sprintf(
		'<button class="rstore-add-to-cart button" data-id="%s" data-quantity="%d" data-redirect="%s">%s</button>',
		esc_attr( $id ),
		absint( $quantity ),
		( $redirect ) ? 'true' : 'false',
		esc_html( $label )
	);

	if ( ! $echo ) {

		return $output;

	}

	echo $output; // xss ok.

}
