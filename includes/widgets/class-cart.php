<?php

namespace Reseller_Store\Widgets;

use Reseller_Store as Store;
use Reseller_Store\Plugin;

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
			Plugin::prefix( 'cart' ),
			esc_html__( 'Reseller Cart', 'reseller-store' ),
			[
				'classname'   => Plugin::prefix( 'cart', true ),
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

		/**
		 * Filter classes to be appended to the Cart widget.
		 *
		 * @since NEXT
		 *
		 * @var array
		 */
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_cart_widget_classes', [] ) );

		if ( ! empty( $instance['hide_empty'] ) ) {

			$classes[] = 'hide-empty';

		}

		if ( $classes ) {

			preg_match( '/class="([^"]*)"/', $args['before_widget'], $matches );

		}

		if ( ! empty( $matches[0] ) && ! empty( $matches[1] ) ) {

			$args['before_widget'] = str_replace(
				$matches[0],
				sprintf( 'class="%s"', implode( ' ', array_merge( explode( ' ', $matches[1] ), $classes ) ) ),
				$args['before_widget']
			);

		}

		echo $args['before_widget']; // xss ok

		if ( ! empty( $instance['title'] ) ) {

			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; // xss ok

		}

		?>
		<div class="rstore-view-cart">
			<a href="<?php echo esc_url( Store\rstore()->api->urls['cart'] ); ?>">
				<?php printf( esc_html_x( 'View Cart %s', 'number of items in cart', 'reseller-store' ), '(<span class="rstore-cart-count">0</span>)' ); ?>
			</a>
		</div>
		<?php

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

		$title      = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Cart', 'reseller-store' );
		$hide_empty = ! empty( $instance['hide_empty'] );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat">
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_empty' ) ); ?>" value="1" class="checkbox" <?php checked( $hide_empty ) ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>"><?php esc_html_e( 'Hide if cart is empty', 'reseller' ); ?></label>
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

		$instance['title']      = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : null;
		$instance['hide_empty'] = isset( $new_instance['hide_empty'] ) ? (bool) absint( $new_instance['hide_empty'] ) : false;

		return $instance;

	}

}
