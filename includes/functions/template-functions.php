<?php

/**
 * Display the price for a given product.
 *
 * @since NEXT
 *
 * @param  int|WP_Post|null $post (optional)
 * @param  bool             $echo (optional)
 *
 * @return string|null
 */
function rstore_price( $post = null, $echo = true ) {

	$post = get_post( $post );

	$list = rstore_get_product_meta( $post->ID, 'listPrice' );

	if ( ! $list ) {

		return;

	}

	$output = sprintf(
		'<span class="rstore-price">%s</span>',
		esc_html( $list )
	);

	if ( $sale = rstore_get_product_meta( $post->ID, 'salePrice' ) ) {

		$output = sprintf(
			'<span class="rstore-price rstore-has-sale-price"><del>%s</del> %s</span>',
			esc_html( $list ),
			esc_html( $sale )
		);

	}

	if ( $term = rstore_get_product_meta( $post->ID, 'term' ) ) {

		$output = sprintf(
			esc_html_x( '%1$s / per %2$s', '1. price, 2. subscription term - e.g. $10 / per month', 'reseller-store' ),
			$output,
			$term // xss ok
		);

	}

	$output = sprintf( '<p class="rstore-pricing">%s</p>', $output );

	if ( ! $echo ) {

		return $output;

	}

	echo $output; // xss ok

}

/**
 * Display an `Add to cart` form for a given product.
 *
 * @since NEXT
 *
 * @param  int|WP_Post|null $post (optional)
 * @param  bool             $echo (optional)
 *
 * @return string|null
 */
function rstore_add_to_cart_form( $post = null, $echo = true ) {

	extract( rstore_get_add_to_cart_vars( $post ) );

	if ( empty( $id ) || empty( $quantity ) || ! isset( $redirect ) || empty( $label ) ) {

		return;

	}

	$redirect = ( $redirect ) ? 'true' : 'false';

	ob_start();

	?>
	<form class="rstore-add-to-cart-form">
		<input type="hidden" class="rstore-quantity" value="<?php echo absint( $quantity ); ?>" min="1" required>
		<input type="submit" class="rstore-add-to-cart submit button" data-id="<?php echo esc_attr( $id ); ?>" data-quantity="<?php echo absint( $quantity ); ?>" data-redirect="<?php echo esc_attr( $redirect ); ?>" value="<?php echo esc_attr( $label ); ?>">
		<img src="<?php echo esc_url( Reseller_Store\Plugin::assets_url( 'images/loading.svg' ) ); ?>" class="rstore-loading">
		<div class="rstore-message"></div>
	</form>
	<?php

	$output = ob_get_clean();

	if ( ! $echo ) {

		return $output;

	}

	echo $output; // xss ok

}

/**
 * Display an `Add to cart` button for a given product.
 *
 * @since NEXT
 *
 * @param  int|WP_Post|null $post (optional)
 * @param  bool             $echo (optional)
 *
 * @return string|null
 */
function rstore_add_to_cart_button( $post = null, $echo = true ) {

	extract( rstore_get_add_to_cart_vars( $post ) );

	if ( empty( $id ) || empty( $quantity ) || ! isset( $redirect ) || empty( $label ) ) {

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

	echo $output; // xss ok

}

/**
 * Display an `Add to cart` link for a given product.
 *
 * @since NEXT
 *
 * @param  int|WP_Post|null $post (optional)
 * @param  bool             $echo (optional)
 *
 * @return string|null
 */
function rstore_add_to_cart_link( $post = null, $echo = true ) {

	extract( rstore_get_add_to_cart_vars( $post ) );

	if ( empty( $id ) || empty( $quantity ) || empty( $label ) || empty( $permalink ) ) {

		return;

	}

	$output = sprintf(
		'<a href="%s" class="rstore-add-to-cart" data-id="%s">%s</a>',
		esc_url( add_query_arg( 'add-to-cart', absint( $quantity ), $permalink ) ),
		esc_attr( $id ),
		esc_html( $label )
	);

	if ( ! $echo ) {

		return $output;

	}

	echo $output; // xss ok

}
