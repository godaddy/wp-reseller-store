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
 * @since    1.0.0
 */

namespace Reseller_Store\Widgets;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
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
			esc_html__( 'Reseller Cart', 'reseller-store' ),
			[
				'classname'   => rstore_prefix( 'cart', true ),
				'description' => esc_html__( "Display the user's cart in the sidebar.", 'reseller-store' ),
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

		$data = $this->get_data( $instance );

		if ( ! empty( $data['title'] ) ) {

			echo $args['before_title'] . apply_filters( 'widget_title', $data['title'] ) . $args['after_title']; // xss ok.

		}

		?>

		<div class="rstore-view-cart">
			<a href="<?php echo esc_url( rstore()->api->urls['cart'] ); ?>">
				<?php echo $data['button_label']; ?> (<span class="rstore-cart-count">0</span>)
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
		$data = $this->get_data( $instance );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>"><?php esc_html_e( 'Button Label:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_label' ) ); ?>" value="<?php echo esc_attr( $data['button_label'] ); ?>" class="widefat">
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

		$instance['title']        = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : null;
		$instance['button_label'] = isset( $new_instance['button_label'] ) ? wp_kses_post( $new_instance['button_label'] ) : null;
		return $instance;

	}

	/**
	 * Set data from instance or default value.
	 *
	 * @since 1.1.0
	 *
	 * @param  array $instance Widget instance.
	 *
	 * @return array
	 */
	private function get_data( $instance ) {
		return array(
			'title'        => isset( $instance['title'] ) ? $instance['title'] : '',
			'button_label' => isset( $instance['button_label'] ) ? $instance['button_label'] : esc_html__( 'View Cart', 'reseller-store' ),
		);
	}

}
