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
	 * @global WP_Post $post
	 * @since  NEXT
	 */
	public function wp_enqueue_scripts() {

		global $post;

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'rstore', rstore()->assets_url . "css/store{$suffix}.css", [], rstore()->version );

		wp_enqueue_script( 'rstore', rstore()->assets_url . "js/store{$suffix}.js", [ 'jquery' ], rstore()->version, true );

		$data = [
			'pl_id'   => (int) Plugin::get_option( 'pl_id' ),
			'urls'    => [
				'cart'     => rstore()->api->urls['cart'],
				'cart_api' => rstore()->api->url( 'cart/{pl_id}' ),
			],
			'product' => [
				'id'      => ( Post_Type::SLUG === $post->post_type ) ? Plugin::get_product_meta( $post->ID, 'id', '' ) : '',
				'post_id' => ( Post_Type::SLUG === $post->post_type ) ? $post->ID : '',
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

		$sale = Plugin::get_product_meta( $post->ID, 'salePrice' );

		$output = ( $sale ) ? sprintf( '<span class="rstore-price rstore-has-sale-price"><del>%s</del> %s</span>', $sale, $list ) : sprintf( '<span class="rstore-price">%s</span>', $list );

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
	private function get_add_to_cart_vars( $post ) {

		$post = get_post( $post );

		return [
			'id'        => Plugin::get_product_meta( $post->ID, 'id' ),
			'redirect'  => (bool) Plugin::get_product_meta( $post->ID, 'add_cart_redirect', false, true ),
			'label'     => Plugin::get_product_meta( $post->ID, 'add_cart_button_label', esc_html__( 'Add to cart', 'reseller-store' ), true ),
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

		if ( empty( $id ) || ! isset( $redirect ) || empty( $label ) ) {

			return;

		}

		$redirect = ( $redirect ) ? 'true' : 'false';

		ob_start();

		?>
		<form class="rstore-add-to-cart-form">
			<input type="number" class="rstore-add-to-cart-quantity" value="1" min="1" required>
			<input type="submit" class="rstore-add-to-cart submit button" data-id="<?php echo esc_attr( $id ); ?>" data-quantity="1" data-redirect="<?php echo esc_attr( $redirect ); ?>" value="<?php echo esc_attr( $label ); ?>">
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

		if ( empty( $id ) || ! isset( $redirect ) || empty( $label ) ) {

			return;

		}

		$output = sprintf(
			'<button class="rstore-add-to-cart button" data-id="%s" data-quantity="1" data-redirect="%s">%s</button>',
			esc_attr( $id ),
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

		if ( empty( $permalink ) || empty( $id ) || empty( $label ) ) {

			return;

		}

		$output = sprintf(
			'<a href="%s" class="rstore-add-to-cart" data-id="%s" data-quantity="1">%s</a>',
			esc_url( add_query_arg( 'add-to-cart', $id, $permalink ) ),
			esc_attr( $id ),
			esc_html( $label )
		);

		if ( ! $echo ) {

			return $output;

		}

		echo $output; // xss ok

	}

}
