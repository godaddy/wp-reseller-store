<?php
/**
 * GoDaddy Reseller Store product widget class.
 *
 * Handles the Reseller store product widget.
 *
 * @class    Reseller_Store/Widgets/Product
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

final class Product extends \WP_Widget {

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			rstore_prefix( 'Product' ),
			esc_html__( 'Reseller Product', 'reseller-store' ),
			[
				'classname'   => rstore_prefix( 'Product', true ),
				'description' => esc_html__( 'Display product post.', 'reseller-store' ),
			]
		);

	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args     Widget arguments array.
	 * @param object $instance Instance object.
	 *
	 * @return mixed           Markup for the single product widget.
	 */
	public function widget( $args, $instance ) {

		global $wp_current_filter, $post;

		$data = $this->get_data( $instance );

		$post_id = $data['post_id'];

		$product = get_post( $post_id );

		if ( null === $product || 'publish' !== $product->post_status ||
			\Reseller_Store\Post_Type::SLUG !== $product->post_type ) {

			if ( Shortcodes::is_widget( $args ) ) {

				esc_html_e( 'Post id is not valid.', 'reseller' );

			}

			return esc_html__( 'Post id is not valid.', 'reseller' );

		}

		/**
		 * Filter classes to be appended to the Product widget.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_product_widget_classes', [] ) );

		if ( $classes ) {

			preg_match( '/class="([^"]*)"/', $args['before_widget'], $matches );

		}

		$content = null;

		if ( ! empty( $matches[0] ) && ! empty( $matches[1] ) ) {

			$content = str_replace(
				$matches[0],
				sprintf( 'class="%s"', implode( ' ', array_merge( explode( ' ', $matches[1] ), $classes ) ) ),
				$args['before_widget']
			);

		} else {

			$content = $args['before_widget']; // xss ok.

		}

		if ( 'none' !== $data['image_size'] ) {

			$content .= get_the_post_thumbnail( $post_id, $data['image_size'] );

		}

		if ( $data['show_title'] ) {

			$content .= $args['before_title'] . apply_filters( 'widget_title', $product->post_title ) . $args['after_title']; // xss ok.

		}

		if ( $data['show_content'] ) {

			$content .= $product->post_content;

		}

		if ( $data['show_price'] ) {

			$content .= rstore_price( $post_id, false );

		}

		if ( ! empty( $data['button_label'] ) ) {

			$content .= rstore_add_to_cart_form( $post_id, false, $data['button_label'], $data['text_cart'], $data['redirect'] ); // xss ok.

		}
		$content .= $args['after_widget']; // xss ok.

		if ( ! in_array( 'the_content', $wp_current_filter, true ) ) {

			$original_post       = $post;
			$post                = $product;
			$post->rstore_widget = true;
			setup_postdata( $product );

			$content = apply_filters( 'the_content', $content );

			$post = $original_post;
			wp_reset_postdata();

		}

		if ( Shortcodes::is_widget( $args ) ) {

			echo $content;

		}

		return $content;

	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {

		$data = $this->get_data( $instance );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>">
				<?php esc_html_e( 'Product: ', 'reseller' ); ?>
			</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_id' ) ); ?>" class="widefat" style="width:100%;">
				<?php self::get_products( $data['post_id'] ); ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>">
				<?php esc_html_e( 'Image size: ', 'reseller' ); ?>
			</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_size' ) ); ?>" class="widefat" style="width:100%;">
				<option value='thumbnail' <?php selected( 'thumbnail', $data['image_size'] ); ?>><?php esc_html_e( 'Thumbnail', 'reseller' ); ?></option>
				<option value='medium' <?php selected( 'medium', $data['image_size'] ); ?>><?php esc_html_e( 'Medium resolution', 'reseller' ); ?></option>
				<option value='large' <?php selected( 'large', $data['image_size'] ); ?>><?php esc_html_e( 'Large resolution', 'reseller' ); ?></option>
				<option value='full' <?php selected( 'full', $data['image_size'] ); ?>><?php esc_html_e( 'Original resolution', 'reseller' ); ?></option>
				<option value='none' <?php selected( 'none', $data['image_size'] ); ?>><?php esc_html_e( 'Hide Image', 'reseller' ); ?></option>
			</select>
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_title' ) ); ?>" value="1" class="checkbox" <?php checked( $data['show_title'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>">
				<?php esc_html_e( 'Show product title', 'reseller' ); ?>
			</label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_content' ) ); ?>" value="1" class="checkbox" <?php checked( $data['show_content'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>">
				<?php esc_html_e( 'Show post text', 'reseller' ); ?>
			</label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_price' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_price' ) ); ?>" value="1" class="checkbox" <?php checked( $data['show_price'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_price' ) ); ?>">
				<?php esc_html_e( 'Show product price', 'reseller' ); ?>
			</label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'redirect' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'redirect' ) ); ?>" value="1" class="checkbox" <?php checked( $data['redirect'], true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'redirect' ) ); ?>">
				<?php esc_html_e( 'Redirect to cart after adding item', 'reseller' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>"><?php esc_html_e( 'Button Label:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_label' ) ); ?>" value="<?php echo esc_attr( $data['button_label'] ); ?>" class="widefat">
			<span class="description" ><?php esc_html_e( 'Leave blank to hide button', 'reseller' ); ?></span>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'text_cart' ) ); ?>"><?php esc_html_e( 'Cart Text:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'text_cart' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_cart' ) ); ?>" value="<?php echo esc_attr( $data['text_cart'] ); ?>" class="widefat">
			<span class="description" ><?php esc_html_e( 'Cart link text', 'reseller' ); ?></span>
		</p>

		<?php

	}

	/**
	 * Processing widget options on save.
	 *
	 * @param array $new_instance New widget options array.
	 * @param array $old_instance New widget options array.
	 *
	 * @since 1.0.0
	 *
	 * @return array              Final array of widget options.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance['post_id']      = isset( $new_instance['post_id'] ) ? sanitize_text_field( $new_instance['post_id'] ) : null;
		$instance['show_title']   = isset( $new_instance['show_title'] ) ? (bool) absint( $new_instance['show_title'] ) : false;
		$instance['show_content'] = isset( $new_instance['show_content'] ) ? (bool) absint( $new_instance['show_content'] ) : false;
		$instance['show_price']   = isset( $new_instance['show_price'] ) ? (bool) absint( $new_instance['show_price'] ) : false;
		$instance['redirect']     = isset( $new_instance['redirect'] ) ? (bool) absint( $new_instance['redirect'] ) : false;
		$instance['image_size']   = isset( $new_instance['image_size'] ) ? sanitize_text_field( $new_instance['image_size'] ) : 'post-thumbnail';
		$instance['button_label'] = isset( $new_instance['button_label'] ) ? sanitize_text_field( $new_instance['button_label'] ) : '';
		$instance['text_cart']    = isset( $new_instance['text_cart'] ) ? sanitize_text_field( $new_instance['text_cart'] ) : '';

		return $instance;

	}

	/**
	 * Retrieve reseller products.
	 *
	 * @param integer $selected_product The selected product ID.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Markup for the product select options.
	 */
	private static function get_products( $selected_product ) {

		$query = new \WP_Query(
			[
				'post_type'   => \Reseller_Store\Post_Type::SLUG,
				'post_status' => 'publish',
				'nopaging'    => true, // get a list of every product.
			]
		);

		$products = '';

		if ( ! $selected_product ) {

			$products .= '<option></option>';

		}

		while ( $query->have_posts() ) {

			$query->the_post();

			$id = get_the_ID();

			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $id ),
				selected( $selected_product, $id, false ),
				esc_html( get_the_title() )
			); // xss ok.

		}

		wp_reset_postdata();

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
		return [
			'post_id'      => (int) isset( $instance['post_id'] ) ? $instance['post_id'] : -1,
			'show_title'   => isset( $instance['show_title'] ) ? ! empty( $instance['show_title'] ) : true,
			'show_content' => isset( $instance['show_content'] ) ? ! empty( $instance['show_content'] ) : true,
			'show_price'   => isset( $instance['show_price'] ) ? ! empty( $instance['show_price'] ) : true,
			'redirect'     => isset( $instance['redirect'] ) ? ! empty( $instance['redirect'] ) : true,
			'button_label' => isset( $instance['button_label'] ) ? $instance['button_label'] : esc_html__( 'Add to cart', 'reseller-store' ),
			'text_cart'    => isset( $instance['text_cart'] ) ? $instance['text_cart'] : esc_html__( 'Continue to cart', 'reseller-store' ),
			'image_size'   => isset( $instance['image_size'] ) ? $instance['image_size'] : 'post-thumbnail',
		];
	}
}
