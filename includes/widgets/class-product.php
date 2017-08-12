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

if ( ! defined( 'ABSPATH' ) ) {

	exit;

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
			array(
				'classname'   => rstore_prefix( 'Product', true ),
				'description' => esc_html__( 'Display product post.', 'reseller-store' ),
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

		if ( ! empty( $matches[0] ) && ! empty( $matches[1] ) ) {

			$args['before_widget'] = str_replace(
				$matches[0],
				sprintf( 'class="%s"', implode( ' ', array_merge( explode( ' ', $matches[1] ), $classes ) ) ),
				$args['before_widget']
			);

		}

		echo $args['before_widget']; // xss ok.

		if ( ! isset( $instance['post_id'] ) ) {

			return;

		}

		$post_id = (int) $instance['post_id'];

		if ( isset( $instance['image_size'] ) ) {

			echo get_the_post_thumbnail( $post_id,  $instance['image_size'] );

		}

		if ( $instance['show_title'] ) {

			echo $args['before_title'] . apply_filters( 'widget_title', get_the_title( $post_id ) ) . $args['after_title']; // xss ok.

		}

		echo wp_kses_post( apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ) );
		echo wp_kses_post( apply_filters( 'the_content', rstore_price( $post_id, false ) ) );
		echo rstore_add_to_cart_form( $post_id, false ); // xss ok.
		echo $args['after_widget']; // xss ok.

	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {

		$post_id    = isset( $instance['post_id'] ) ? $instance['post_id'] : false;
		$show_title = isset( $instance['show_title'] ) ? ! empty( $instance['show_title'] ) : true;
		$image_size = isset( $instance['image_size'] ) ? $instance['image_size'] : 'thumbnail';

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>">
				<?php esc_html_e( 'Product:', 'reseller' ); ?>
			</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_id' ) ); ?>" class="widefat" style="width:100%;">
				<?php self::get_products( $post_id ); ?>
			</select>
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_title' ) ); ?>" value="1" class="checkbox" <?php checked( $show_title, true ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>">
				<?php esc_html_e( 'Show product title', 'reseller' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>">
				<?php esc_html_e( 'Image size', 'reseller' ); ?>
			</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_size' ) ); ?>" class="widefat" style="width:100%;">
				<option value='thumbnail' <?php selected( 'thumbnail', $image_size ); ?>><?php esc_html_e( 'Thumbnail', 'reseller' ); ?></option>
				<option value='medium' <?php selected( 'medium', $image_size ); ?>><?php esc_html_e( 'Medium resolution', 'reseller' ); ?></option>
				<option value='large' <?php selected( 'large', $image_size ); ?>><?php esc_html_e( 'Large resolution', 'reseller' ); ?></option>
				<option value='full' <?php selected( 'full', $image_size ); ?>><?php esc_html_e( 'Original resolution', 'reseller' ); ?></option>
				<option value='none' <?php selected( 'none', $image_size ); ?>><?php esc_html_e( 'Hide Image', 'reseller' ); ?></option>
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

		$instance['post_id']    = isset( $new_instance['post_id'] ) ? sanitize_text_field( $new_instance['post_id'] ) : null;
		$instance['show_title'] = isset( $new_instance['show_title'] ) ? (bool) absint( $new_instance['show_title'] ) : false;
		$instance['image_size'] = isset( $new_instance['image_size'] ) ? sanitize_text_field( $new_instance['image_size'] ) : 'thumbnail';

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
}
