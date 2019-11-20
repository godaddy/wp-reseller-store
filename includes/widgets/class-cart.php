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

use Reseller_Store\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Cart extends Widget_Base {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		parent::__construct(
			rstore_prefix( 'cart' ),
			esc_html__( 'Reseller Cart Link', 'reseller-store' ),
			array(
				'classname'   => rstore_prefix( 'cart', true ),
				'description' => esc_html__( 'A shopper cart status.', 'reseller-store' ),
			)
		);

	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 0.2.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 *
	 * @return mixed Returns the HTML markup for the domain transfer container.
	 */
	public function widget( $args, $instance ) {
		/**
		 * Filter classes to be appended to the Cart widget.
		 *
		 * @since 0.2.0
		 *
		 * @var array
		 */
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_cart_widget_classes', array() ) );

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

		ob_start();

		echo $args['before_widget']; // xss ok.

		$data = $this->get_data( $instance );

		if ( ! empty( $data['title'] ) ) {

			echo $args['before_title'] . apply_filters( 'widget_title', $data['title'] ) . $args['after_title']; // xss ok.

		}

		?>

		<div class="rstore-view-cart">
			<a href="<?php echo esc_url_raw( rstore()->api->url( 'cart' ), 'https' ); ?>">
				<?php echo esc_html( $data['button_label'] ); ?> (<span class="rstore-cart-count">0</span>)
			</a>
		</div>

		<?php

		echo $args['after_widget']; // xss ok.

		$cart_widget = ob_get_contents();
		ob_get_clean();

		$cart_widget = apply_filters( 'rstore_cart_button_html', $cart_widget );

		$is_widget = apply_filters( 'rstore_is_widget', $args );
		if ( $is_widget ) {

			echo $cart_widget; // xss ok.

		}

		return $cart_widget;

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
		$this->display_form_input( 'title', $data['title'], __( 'Title', 'reseller-store' ) );
		$this->display_form_input( 'button_label', $data['button_label'], __( 'Button', 'reseller-store' ) );

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
			'title'        => isset( $instance['title'] ) ? $instance['title'] : apply_filters( 'rstore_cart_title', '' ),
			'button_label' => isset( $instance['button_label'] ) ? $instance['button_label'] : apply_filters( 'rstore_cart_button_label', esc_html__( 'View Cart', 'reseller-store' ) ),
		);
	}

}
