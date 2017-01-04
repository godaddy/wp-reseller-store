<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Display {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );

	}

	/**
	 * Enqueue front-end scripts.
	 *
	 * @action wp_enqueue_scripts
	 * @since  NEXT
	 */
	public function wp_enqueue_scripts() {

		$rtl = is_rtl() ? '-rtl' : '';

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'rstore', rstore()->assets_url . "css/store{$rtl}{$suffix}.css", [ 'dashicons' ], rstore()->version );

		wp_enqueue_script( 'rstore', rstore()->assets_url . "js/store{$suffix}.js", [ 'jquery' ], rstore()->version, true );

		/**
		 * Filter the TTL for cookies (in seconds).
		 *
		 * @since NEXT
		 *
		 * @var int
		 */
		$cookie_ttl = (int) apply_filters( 'rstore_cookie_ttl', DAY_IN_SECONDS * 30 );

		$data = [
			'pl_id'   => (int) Plugin::get_option( 'pl_id' ),
			'urls'    => [
				'cart'     => esc_url( rstore()->api->urls['cart'] ),
				'cart_api' => esc_url( rstore()->api->url( 'cart/{pl_id}' ) ),
			],
			'cookies' => [
				'ttl'       => absint( $cookie_ttl ) * 1000, // Convert seconds to ms
				'cartCount' => Plugin::prefix( 'cart-count', true ),
			],
			'product' => [
				'id' => ( Post_Type::SLUG === get_post_type() ) ? Plugin::get_product_meta( get_the_ID(), 'id', '' ) : '',
			],
			'i18n'    => [
				'view_cart' => esc_html__( 'View cart', 'reseller-store' ),
				'error'     => esc_html__( 'An unknown error has occurred', 'reseller-store' ),
			],
		];

		wp_localize_script( 'rstore', 'rstore', $data );

	}

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
	public static function price( $post = null, $echo = true ) {

		$post = get_post( $post );

		$list = Plugin::get_product_meta( $post->ID, 'listPrice' );

		if ( ! $list ) {

			return;

		}

		$output = sprintf(
			'<span class="rstore-price">%s</span>',
			esc_html( $list )
		);

		if ( $sale = Plugin::get_product_meta( $post->ID, 'salePrice' ) ) {

			$output = sprintf(
				'<span class="rstore-price rstore-has-sale-price"><del>%s</del> %s</span>',
				esc_html( $list ),
				esc_html( $sale )
			);

		}

		if ( ! $echo ) {

			return $output;

		}

		echo $output; // xss ok

	}

	/**
	 * Return vars needed for displaying `Add to cart` markup.
	 *
	 * @since NEXT
	 *
	 * @param  int|WP_Post|null $post
	 *
	 * @return array
	 */
	private static function get_add_to_cart_vars( $post ) {

		$post = get_post( $post );

		return [
			'id'        => Plugin::get_product_meta( $post->ID, 'id' ),
			'quantity'  => (int) Plugin::get_product_meta( $post->ID, 'default_quantity', 1, true ),
			'redirect'  => (bool) Plugin::get_product_meta( $post->ID, 'add_to_cart_redirect', false, true ),
			'label'     => Plugin::get_product_meta( $post->ID, 'add_to_cart_button_label', esc_html__( 'Add to cart', 'reseller-store' ), true ),
			'permalink' => get_permalink( $post->ID ),
		];

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
	public static function add_to_cart_form( $post = null, $echo = true ) {

		extract( self::get_add_to_cart_vars( $post ) );

		if ( empty( $id ) || empty( $quantity ) || ! isset( $redirect ) || empty( $label ) ) {

			return;

		}

		$redirect = ( $redirect ) ? 'true' : 'false';

		ob_start();

		?>
		<form class="rstore-add-to-cart-form">
			<input type="number" class="rstore-quantity" value="<?php echo absint( $quantity ); ?>" min="1" required>
			<input type="submit" class="rstore-add-to-cart submit button" data-id="<?php echo esc_attr( $id ); ?>" data-quantity="<?php echo absint( $quantity ); ?>" data-redirect="<?php echo esc_attr( $redirect ); ?>" value="<?php echo esc_attr( $label ); ?>">
			<img src="<?php echo esc_url( rstore()->assets_url . 'images/loading.svg' ); ?>" class="rstore-loading">
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
	public static function add_to_cart_button( $post = null, $echo = true ) {

		extract( self::get_add_to_cart_vars( $post ) );

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
	public static function add_to_cart_link( $post = null, $echo = true ) {

		extract( self::get_add_to_cart_vars( $post ) );

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

}
