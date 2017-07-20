<?php
/**
 * GoDaddy Reseller Store cart widget class.
 *
 * Handles the Reseller store cart widget.
 *
 * @class    Reseller_Store/Widgets/Cart
 * @package  WP_Widget
 * @category Class
 * @author   GoDaddy
 * @since    NEXT
 */

namespace Reseller_Store\Widgets;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Cart extends \WP_Widget {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		parent::__construct(
			rstore_prefix( 'cart' ),
			esc_html__( 'Reseller Cart', 'godaddy-reseller-store' ),
			[
				'classname'   => rstore_prefix( 'cart', true ),
				'description' => esc_html__( "Display the user's cart in the sidebar.", 'godaddy-reseller-store' ),
			]
		);

	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 0.2.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {

		/**
		 * Filter classes to be appended to the Cart widget.
		 *
		 * @since 0.2.0
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

		echo $args['before_widget']; // xss ok.

		if ( ! empty( $instance['title'] ) ) {

			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; // xss ok.

		}

		?>
		<div class="rstore-view-cart">
			<span class="dashicons dashicons-cart"></span>
			<a href="<?php echo esc_url( rstore()->api->urls['cart'] ); ?>">
				<?php
				/* translators: number of items in cart */
				printf( esc_html__( 'View Cart %s', 'godaddy-reseller-store' ), '(<span class="rstore-cart-count">0</span>)' );
				?>
			</a>
		</div>
		<?php

		echo $args['after_widget']; // xss ok.

	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @since 0.2.0
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {

		$title      = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Cart', 'godaddy-reseller-store' );
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
	 * @since 0.2.0
	 *
	 * @param  array $new_instance New widget instance.
	 * @param  array $old_instance Old widget instance.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance['title']      = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : null;
		$instance['hide_empty'] = isset( $new_instance['hide_empty'] ) ? (bool) absint( $new_instance['hide_empty'] ) : false;

		return $instance;

	}

}
