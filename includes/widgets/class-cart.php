<?php

namespace Reseller_Store\Widgets;

use Reseller_Store as Plugin;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Cart extends \WP_Widget {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		parent::__construct(
			'rstore_cart',
			esc_html__( 'Reseller Cart', 'reseller-store' ),
			[
				'classname'   => 'rstore_cart',
				'description' => esc_html__( "Display the user's cart in the sidebar.", 'reseller-store' ),
			]
		);

	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since NEXT
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'js-cookie', "https://cdnjs.cloudflare.com/ajax/libs/js-cookie/2.1.3/js.cookie{$suffix}.js", [], '2.1.3', true );

		wp_enqueue_script( 'rstore-cart', Plugin\rstore()->assets_url . "js/cart{$suffix}.js", [ 'jquery', 'js-cookie' ], Plugin\rstore()->version, true );

		wp_localize_script(
			'rstore-cart',
			'rstore',
			[
				'pl_id'        => (int) Plugin\rstore()->get_option( 'pl_id' ),
				'cart_url'     => Plugin\rstore()->api->urls['cart'], // xss ok
				'cart_api_url' => Plugin\rstore()->api->url( 'cart/{pl_id}' ), // xss ok
			]
		);

		echo $args['before_widget']; // xss ok

		if ( ! empty( $instance['title'] ) ) {

			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; // xss ok

		}

		printf(
			'<div class="rstore-view-cart"><a href="%s" class="rstore-view-cart">%s</a></div>',
			esc_url( Plugin\rstore()->api->urls['cart'] ),
			sprintf(
				esc_html_x( 'View Cart %s', 'number of items in cart', 'reseller-store' ),
				'(<span class="rstore-cart-count">0</span>)'
			)
		);

		echo $args['after_widget']; // xss ok

	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @since NEXT
	 *
	 * @param array $instance
	 */
	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : null;

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat">
		</p>
		<?php

	}

	/**
	 * Processing widget options on save.
	 *
	 * @since NEXT
	 *
	 * @param  array $new_instance
	 * @param  array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$title = sanitize_text_field( $new_instance['title'] );

		$instance['title'] = ! empty( $title ) ? $title : null;

		return $instance;

	}

}
