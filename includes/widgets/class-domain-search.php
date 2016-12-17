<?php

namespace Reseller_Store\Widgets;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Domain_Search extends \WP_Widget {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		parent::__construct(
			'rstore_domain_search',
			esc_html__( 'Reseller Domain Search', 'reseller-store' ),
			[
				'classname'   => 'rstore_domain_search',
				'description' => esc_html__( 'A search form for domain names.', 'reseller-store' ),
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
		 * Filter classes to be appended to the Domain Search widget.
		 *
		 * The `widget_search` class is added here to be sure our
		 * Domain Search widget inherits any default Search widget
		 * styles included by a theme.
		 *
		 * @since NEXT
		 *
		 * @var array
		 */
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_domain_search_widget_classes', [ 'widget_search' ] ) );

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

		$placeholder  = ! empty( $instance['placeholder'] ) ? $instance['placeholder'] : esc_attr__( 'Find your perfect domain name', 'reseller-store' );
		$button_label = ! empty( $instance['button_label'] ) ? $instance['button_label'] : null;

		?>
		<form role="search" method="get" class="search-form rstore-domain-search-form" action="<?php echo esc_url( home_url() ); ?>">
			<label>
				<span class="screen-reader-text"><?php esc_html_e( 'Search for a domain name:', 'reseller-store' ); ?></span>
				<input type="search" name="domain-name-search" value="" class="search-field rstore-domain-search-field" placeholder="<?php echo esc_attr( $placeholder ); ?>">
				<?php if ( $button_label ) : ?>
					<input type="submit" value="<?php echo esc_attr( $button_label ); ?>">
				<?php endif; ?>
			</label>
		</form>
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

		$placeholder  = ! empty( $instance['placeholder'] ) ? $instance['placeholder'] : null;
		$button_label = ! empty( $instance['button_label'] ) ? $instance['button_label'] : null;

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>"><?php esc_html_e( 'Placeholder:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder' ) ); ?>" value="<?php echo esc_attr( $placeholder ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Find your perfect domain name', 'reseller-store' ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>"><?php esc_html_e( 'Button:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_label' ) ); ?>" value="<?php echo esc_attr( $button_label ); ?>" class="widefat">
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

		$placeholder  = sanitize_text_field( $new_instance['placeholder'] );
		$button_label = sanitize_text_field( $new_instance['button_label'] );

		$instance['placeholder']  = ! empty( $placeholder ) ? $placeholder : null;
		$instance['button_label'] = ! empty( $button_label ) ? $button_label : null;

		return $instance;

	}

}
