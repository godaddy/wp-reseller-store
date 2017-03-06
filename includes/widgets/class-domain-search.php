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
			rstore_prefix( 'domain-search' ),
			esc_html__( 'Reseller Domain Search', 'reseller-store' ),
			[
				'classname'   => rstore_prefix( 'domain-search', true ),
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

		$placeholder  = ! empty( $instance['placeholder'] ) ? $instance['placeholder'] : null;
		$button_label = ! empty( $instance['button_label'] ) ? $instance['button_label'] : null;

		?>
		<form role="search" method="post" class="search-form rstore-domain-search-form" action="<?php echo esc_url( rstore()->api->urls['domain_search'] ); ?>" novalidate>
			<label class="screen-reader-text" for="rstore-domain-search-field"><?php esc_html_e( 'Search for a domain name:', 'reseller-store' ); ?></label>
			<input type="search" name="domainToCheck" id="rstore-domain-search-field" class="search-field required" placeholder="<?php echo esc_attr( $placeholder ); ?>" title="<?php esc_attr_e( 'Search for a domain name:', 'reseller-store' ); ?>" value="" required="required">
			<?php if ( $button_label ) : ?>
				<button type="submit" class="search submit rstore-domain-search-submit"><?php echo esc_html( $button_label ); ?></button>
			<?php else : ?>
				<input type="submit" class="screen-reader-text search-submit rstore-domain-search-submit" value="<?php esc_attr_e( 'Search', 'reseller-store' ); ?>">
			<?php endif; ?>
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

		$title        = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Domain Search', 'reseller-store' );
		$placeholder  = isset( $instance['placeholder'] ) ? $instance['placeholder'] : esc_html__( 'Find your perfect name', 'reseller-store' );
		$button_label = isset( $instance['button_label'] ) ? $instance['button_label'] : esc_html__( 'Search', 'reseller-store' );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>"><?php esc_html_e( 'Placeholder:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder' ) ); ?>" value="<?php echo esc_attr( $placeholder ); ?>" class="widefat">
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

		$instance['title']        = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : null;
		$instance['placeholder']  = isset( $new_instance['placeholder'] ) ? sanitize_text_field( $new_instance['placeholder'] ) : null;
		$instance['button_label'] = isset( $new_instance['button_label'] ) ? sanitize_text_field( $new_instance['button_label'] ) : null;

		return $instance;

	}

}
