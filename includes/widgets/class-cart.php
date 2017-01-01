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
			Plugin\Plugin::prefix( 'cart' ),
			esc_html__( 'Reseller Cart', 'reseller-store' ),
			[
				'classname'   => Plugin\Plugin::prefix( 'cart' ),
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

		echo $args['before_widget']; // xss ok

		if ( ! empty( $instance['title'] ) ) {

			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; // xss ok

		}

		printf(
			'<div class="rstore-view-cart %s"><a href="%s" class="rstore-view-cart-link">%s</a></div>',
			! empty( $instance['hide_empty'] ) ? 'rstore-hide-empty-cart' : null,
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

		$title      = ! empty( $instance['title'] ) ? $instance['title'] : null;
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

		$title      = sanitize_text_field( $new_instance['title'] );
		$hide_empty = absint( $new_instance['hide_empty'] );

		$instance['title']      = ! empty( $title ) ? $title : null;
		$instance['hide_empty'] = ! empty( $hide_empty );

		return $instance;

	}

}
