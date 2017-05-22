<?php

namespace Reseller_Store\Widgets;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Product extends \WP_Widget {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
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
	 * @since NEXT
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		/**
		 * Filter classes to be appended to the Product widget.
		 *
		 * @since NEXT
		 *
		 * @var array
		 */
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_product_widget_classes', [] ) );

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

		if ( ! isset( $instance['post_id'] ) ) {
			return;
		}

		$post_id = $instance['post_id'];

		if ( isset( $instance['image_size'] ) ) {

			echo get_the_post_thumbnail( $post_id,  $instance['image_size'] );

		}

		if ( $instance['show_title'] ) {

			echo $args['before_title'] . apply_filters( 'widget_title', get_the_title( $post_id ) ) . $args['after_title']; // xss ok

		}

		echo wpautop( get_post_field( 'post_content', $post_id ) );
		echo wpautop( rstore_price( $post_id, false ) );
		echo rstore_add_to_cart_form( $post_id, false );
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

		$post_id      = isset( $instance['post_id'] ) ? $instance['post_id'] : -1;
		$show_title = isset( $instance['show_title'] ) ? ! empty( $instance['show_title'] ) : true;
		$image_size = isset( $instance['image_size'] ) ? $instance['image_size'] : 'thumbnail';

		$query = new \WP_Query(array(
			'post_type' => \Reseller_Store\Post_Type::SLUG,
			'post_status' => 'publish',
			'posts_per_page' => -1,
		));

		$products = '';

		if ( ! isset( $instance['post_id'] ) ) {
			$products .= '<option></option>';
		}

		while ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
			$title = get_the_Title();
			$products .= '<option value="' . $id . '" ' . selected( $post_id, $id ) . '>' . $title . '</option>';
		}

		wp_reset_query();

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>"><?php esc_html_e( 'Product:', 'reseller' ); ?></label>
			<select id=id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>" class="widefat" style="width:100%;">
			<?php echo $products ?>
			</select>

		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_title' ) ); ?>" value="1" class="checkbox" <?php checked( $show_title ) ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>"><?php esc_html_e( 'Show product title', 'reseller' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>"><?php esc_html_e( 'Image size', 'reseller' ); ?></label>
			<select id=id="<?php echo $this->get_field_id( 'image_size' ); ?>" name="<?php echo $this->get_field_name( 'image_size' ); ?>" class="widefat" style="width:100%;">
				<option value='thumbnail' <?php selected( 'thumbnail', $image_size ) ?>><?php esc_html_e( 'Thumbnail', 'reseller' ); ?></option>
				<option value='medium' <?php selected( 'medium', $image_size ) ?>><?php esc_html_e( 'Medium resolution', 'reseller' ); ?></option>
				<option value='large' <?php selected( 'large', $image_size ) ?>><?php esc_html_e( 'Large resolution', 'reseller' ); ?></option>
				<option value='full' <?php selected( 'full', $image_size ) ?>><?php esc_html_e( 'Original resolution', 'reseller' ); ?></option>
				<option value='none' <?php selected( 'none', $image_size ) ?>><?php esc_html_e( 'Hide Image', 'reseller' ); ?></option>
			</select>
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

		$instance['post_id'] = isset( $new_instance['post_id'] ) ? sanitize_text_field( $new_instance['post_id'] ) : null;
		$instance['show_title'] = isset( $new_instance['show_title'] ) ? (bool) absint( $new_instance['show_title'] ) : false;
		$instance['image_size'] = isset( $new_instance['image_size'] )  && $new_instance['image_size'] !== 'none' ? sanitize_text_field( $new_instance['image_size'] ) : null;

		return $instance;

	}

}
