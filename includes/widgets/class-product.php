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

use Reseller_Store\Product_Icons;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Product extends Widget_Base {

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			rstore_prefix( 'product' ),
			esc_html__( 'Reseller Product', 'reseller-store' ),
			array(
				'classname'   => rstore_prefix( 'Product', true ),
				'description' => __( 'Display a product post.', 'reseller-store' ),
				'category'    => __( 'Reseller Store Modules', 'reseller-store' ),
			)
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

		$is_widget = apply_filters( 'rstore_is_widget', $args );

		$product = get_post( $post_id );

		if ( null === $product || 'publish' !== $product->post_status ||
			\Reseller_Store\Post_Type::SLUG !== $product->post_type ) {

			if ( $is_widget ) {

				esc_html_e( 'Post id is not valid.', 'reseller-store' );

			}

			return esc_html__( 'Post id is not valid.', 'reseller-store' );

		}

		/**
		 * Filter classes to be appended to the Product widget.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_product_widget_classes', array() ) );

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

		$content .= Product_Icons::get_product_icon( $product, $data['image_size'] );

		$content .= '<div class="rstore-product-header">';

		if ( $data['show_title'] ) {

			$content .= $args['before_title'] . apply_filters( 'widget_title', $product->post_title ) . $args['after_title']; // xss ok.

		}

		if ( 'classic' !== $data['layout_type'] ) {

			if ( $data['show_price'] ) {

				$content .= rstore_price( $post_id );

			}

			if ( ! empty( $data['button_label'] ) ) {

				$content .= rstore_add_to_cart_form( $post_id, false, $data['button_label'], $data['button_new_tab'], $data['text_cart'], $data['redirect'] ); // xss ok.

			}
		}

		$content .= '</div>';

		if ( $data['show_content'] ) {

			$product_content = $product->post_content;

			if ( ! in_array( 'the_content', $wp_current_filter, true ) ) {

				$original_post = $post;
				$post          = $product;
				setup_postdata( $product );

				$product_content = apply_filters( 'the_content', $product_content );

				$post = $original_post;
				wp_reset_postdata();

			}

			$style = '';

			if ( $data['content_height'] > 0 ) {

				$style = 'height:' . $data['content_height'] . 'px';
			}

			$content .= sprintf( '<div class="rstore-product-summary" style="%s">%s</div>', esc_attr( $style ), $product_content );

			if ( $data['content_height'] > 0 ) {

				$content .= sprintf( '<div class="rstore-product-permalink"><a class="link" href="%s" >%s</a></div>', get_permalink( $post_id ), esc_html( $data['text_more'] ) );

			}
		}

		if ( 'classic' === $data['layout_type'] ) {

			if ( $data['show_price'] ) {

				$content .= rstore_price( $post_id );

			}

			if ( ! empty( $data['button_label'] ) ) {

				$content .= rstore_add_to_cart_form( $post_id, false, $data['button_label'], $data['button_new_tab'], $data['text_cart'], $data['redirect'] );

			}
		}

		$content .= $args['after_widget']; // xss ok.

		$content = apply_filters( 'rstore_product_html', $content );

		if ( $is_widget ) {

			echo $content; // xss ok.

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
				<?php esc_html_e( 'Product', 'reseller-store' ); ?>
			</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_id' ) ); ?>" class="widefat" style="width:100%;">
				<?php self::get_products( $data['post_id'] ); ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>">
				<?php esc_html_e( 'Image Size', 'reseller-store' ); ?>
			</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_size' ) ); ?>" class="widefat" style="width:100%;">
				<option value='icon' <?php selected( 'icon', $data['image_size'] ); ?>><?php esc_html_e( 'Product Icon', 'reseller-store' ); ?></option>
				<option value='thumbnail' <?php selected( 'thumbnail', $data['image_size'] ); ?>><?php esc_html_e( 'Thumbnail', 'reseller-store' ); ?></option>
				<option value='medium' <?php selected( 'medium', $data['image_size'] ); ?>><?php esc_html_e( 'Medium resolution', 'reseller-store' ); ?></option>
				<option value='large' <?php selected( 'large', $data['image_size'] ); ?>><?php esc_html_e( 'Large resolution', 'reseller-store' ); ?></option>
				<option value='full' <?php selected( 'full', $data['image_size'] ); ?>><?php esc_html_e( 'Original resolution', 'reseller-store' ); ?></option>
				<option value='none' <?php selected( 'none', $data['image_size'] ); ?>><?php esc_html_e( 'Hide image', 'reseller-store' ); ?></option>
			</select>
		</p>

		<?php
		$this->display_form_input( 'content_height', $data['content_height'], __( 'Content height', 'reseller-store' ), 'number', __( 'Height in pixels', 'reseller-store' ) );
		$this->display_form_input( 'button_label', $data['button_label'], __( 'Button', 'reseller-store' ), 'text', __( 'Leave blank to hide button', 'reseller-store' ) );
		$this->display_form_checkbox( 'show_title', $data['show_title'], __( 'Show product title', 'reseller-store' ) );
		$this->display_form_checkbox( 'show_content', $data['show_content'], __( 'Show post content', 'reseller-store' ) );
		$this->display_form_checkbox( 'show_price', $data['show_price'], __( 'Show product price', 'reseller-store' ) );
		$this->display_form_checkbox( 'redirect', $data['redirect'], __( 'Redirect to cart after adding item', 'reseller-store' ) );
		$this->display_form_input( 'text_cart', $data['text_cart'], __( 'Cart Link', 'reseller-store' ), 'text', __( 'Cart link text', 'reseller-store' ) );
		$this->display_form_input( 'text_more', $data['text_more'], __( 'Product Permalink', 'reseller-store' ), 'text', __( 'Permalink text', 'reseller-store' ) );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'layout_type' ) ); ?>">
				<?php esc_html_e( 'Layout Type', 'reseller-store' ); ?>
			</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'layout_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'layout_type' ) ); ?>" class="widefat" style="width:100%;">
				<option value='default' <?php selected( 'default', $data['layout_type'] ); ?>><?php esc_html_e( 'Default', 'reseller-store' ); ?></option>
				<option value='classic' <?php selected( 'classic', $data['layout_type'] ); ?>><?php esc_html_e( 'Classic', 'reseller-store' ); ?></option>
			</select>
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

		$instance['post_id']        = isset( $new_instance['post_id'] ) ? absint( $new_instance['post_id'] ) : null;
		$instance['show_title']     = isset( $new_instance['show_title'] ) ? (bool) absint( $new_instance['show_title'] ) : false;
		$instance['show_content']   = isset( $new_instance['show_content'] ) ? (bool) absint( $new_instance['show_content'] ) : false;
		$instance['show_price']     = isset( $new_instance['show_price'] ) ? (bool) absint( $new_instance['show_price'] ) : false;
		$instance['redirect']       = isset( $new_instance['redirect'] ) ? (bool) absint( $new_instance['redirect'] ) : false;
		$instance['image_size']     = isset( $new_instance['image_size'] ) ? sanitize_text_field( $new_instance['image_size'] ) : null;
		$instance['button_label']   = isset( $new_instance['button_label'] ) ? sanitize_text_field( $new_instance['button_label'] ) : '';
		$instance['button_new_tab'] = isset( $new_instance['button_new_tab'] ) ? sanitize_text_field( $new_instance['button_new_tab'] ) : '';
		$instance['text_cart']      = isset( $new_instance['text_cart'] ) ? sanitize_text_field( $new_instance['text_cart'] ) : '';
		$instance['text_more']      = isset( $new_instance['text_more'] ) ? sanitize_text_field( $new_instance['text_more'] ) : '';
		$instance['content_height'] = isset( $new_instance['content_height'] ) ? absint( $new_instance['content_height'] ) : null;
		$instance['layout_type']    = isset( $new_instance['layout_type'] ) ? sanitize_text_field( $new_instance['layout_type'] ) : null;

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
			array(
				'post_type'   => \Reseller_Store\Post_Type::SLUG,
				'post_status' => 'publish',
				'nopaging'    => true, // get a list of every product.
			)
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

		return array(
			'post_id'        => (int) isset( $instance['post_id'] ) ? $instance['post_id'] : -1,
			'show_title'     => isset( $instance['show_title'] ) ? ! empty( $instance['show_title'] ) : apply_filters( 'rstore_product_show_title', true ),
			'show_content'   => isset( $instance['show_content'] ) ? ! empty( $instance['show_content'] ) : apply_filters( 'rstore_product_show_content', true ),
			'show_price'     => isset( $instance['show_price'] ) ? ! empty( $instance['show_price'] ) : apply_filters( 'rstore_product_show_price', true ),
			'redirect'       => isset( $instance['redirect'] ) ? ! empty( $instance['redirect'] ) : apply_filters( 'rstore_product_redirect', true ),
			'button_label'   => isset( $instance['button_label'] ) ? $instance['button_label'] : apply_filters( 'rstore_product_button_label', esc_html__( 'Add to cart', 'reseller-store' ) ),
			'button_new_tab' => isset( $instance['button_new_tab'] ) ? $instance['button_new_tab'] : false,
			'text_cart'      => isset( $instance['text_cart'] ) ? $instance['text_cart'] : apply_filters( 'rstore_product_text_cart', esc_html__( 'Continue to cart', 'reseller-store' ) ),
			'text_more'      => isset( $instance['text_more'] ) ? $instance['text_more'] : apply_filters( 'rstore_product_text_more', esc_html__( 'More info', 'reseller-store' ) ),
			'content_height' => isset( $instance['content_height'] ) ? intval( $instance['content_height'] ) : apply_filters( 'rstore_product_content_height', 250 ),
			'image_size'     => isset( $instance['image_size'] ) ? $instance['image_size'] : apply_filters( 'rstore_product_image_size', 'icon' ),
			'layout_type'    => isset( $instance['layout_type'] ) ? $instance['layout_type'] : apply_filters( 'rstore_product_layout_type', 'default' ),
		);
	}
}
