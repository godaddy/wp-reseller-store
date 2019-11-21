<?php
/**
 * GoDaddy Reseller Store domain search widget class.
 *
 * Handles the Reseller store domain search widget.
 *
 * @class    Reseller_Store/Widgets/Domain_Search
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

final class Domain_Search extends Widget_Base {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		parent::__construct(
			rstore_prefix( 'domain' ),
			esc_html__( 'Reseller Advanced Domain Search', 'reseller-store' ),
			array(
				'classname'   => rstore_prefix( 'domain', true ),
				'description' => esc_html__( 'An advanced search form with on page results for domain names.', 'reseller-store' ),
				'category'    => __( 'Reseller Store Modules', 'reseller-store' ),
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
	 * @return mixed Returns the HTML markup for the domain search container.
	 */
	public function widget( $args, $instance ) {

		/**
		 * Filter classes to be appended to the Domain Search widget.
		 *
		 * The `widget_search` class is added here to be sure our
		 * Domain Search widget inherits any default Search widget
		 * styles included by a theme.
		 *
		 * @since 0.2.0
		 *
		 * @var array
		 */
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_domain_search_widget_classes', array( 'rstore_domain_placeholder' ) ) );

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

		$classes = 'rstore-domain-search';
		$plid    = rstore_get_option( 'pl_id' );
		if ( $data['modal'] ) {
			$classes .= ' rstore-domain-popup';
		}

		echo "<div class=\"$classes\" data-plid=\"$plid\"";

		foreach ( $data as $key => $text ) {
			if ( ! empty( $text ) ) {
				echo ' data-' . $key . '="' . $text . '"';
			}
		}

		echo '>';

		esc_html_e( 'Domain Search', 'reseller-store' );

		echo '</div>';

		echo $args['after_widget']; // xss ok.

		$domain_search_widget = ob_get_contents();
		ob_get_clean();

		$domain_search_widget = apply_filters( 'rstore_domain_search_html', $domain_search_widget );

		if ( apply_filters( 'rstore_is_widget', $args ) ) {

			echo $domain_search_widget; // xss ok.

		}

		return $domain_search_widget;

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
		$this->display_form_input( 'text_placeholder', $data['text_placeholder'], __( 'Placeholder', 'reseller-store' ) );
		$this->display_form_input( 'text_search', $data['text_search'], __( 'Search Button', 'reseller-store' ) );
		$this->display_form_input( 'page_size', $data['page_size'], __( 'Page Size', 'reseller-store' ), 'number' );
		$this->display_form_input( 'text_available', $data['text_available'], __( 'Available Text', 'reseller-store' ) );
		$this->display_form_input( 'text_not_available', $data['text_not_available'], __( 'Not Available Text', 'reseller-store' ) );
		$this->display_form_input( 'text_cart', $data['text_cart'], __( 'Cart Button', 'reseller-store' ) );
		$this->display_form_input( 'text_select', $data['text_select'], __( 'Select Button', 'reseller-store' ) );
		$this->display_form_input( 'text_selected', $data['text_selected'], __( 'Deselect Button', 'reseller-store' ) );
		$this->display_form_checkbox( 'modal', $data['modal'], __( 'Display results in a modal', 'reseller-store' ) );
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

		$instance['title']              = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : null;
		$instance['page_size']          = isset( $new_instance['page_size'] ) ? absint( $new_instance['page_size'] ) : null;
		$instance['text_placeholder']   = isset( $new_instance['text_placeholder'] ) ? wp_kses_post( $new_instance['text_placeholder'] ) : null;
		$instance['text_search']        = isset( $new_instance['text_search'] ) ? wp_kses_post( $new_instance['text_search'] ) : null;
		$instance['text_available']     = isset( $new_instance['text_available'] ) ? wp_kses_post( $new_instance['text_available'] ) : null;
		$instance['text_not_available'] = isset( $new_instance['text_not_available'] ) ? wp_kses_post( $new_instance['text_not_available'] ) : null;
		$instance['text_cart']          = isset( $new_instance['text_cart'] ) ? wp_kses_post( $new_instance['text_cart'] ) : null;
		$instance['text_select']        = isset( $new_instance['text_select'] ) ? wp_kses_post( $new_instance['text_select'] ) : null;
		$instance['text_selected']      = isset( $new_instance['text_selected'] ) ? wp_kses_post( $new_instance['text_selected'] ) : null;
		$instance['modal']              = isset( $new_instance['modal'] ) ? (bool) absint( $new_instance['modal'] ) : null;

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
			'title'              => isset( $instance['title'] ) ? $instance['title'] : apply_filters( 'rstore_domain_title', '' ),
			'page_size'          => isset( $instance['page_size'] ) ? $instance['page_size'] : apply_filters( 'rstore_domain_page_size', 5 ),
			'text_placeholder'   => isset( $instance['text_placeholder'] ) ? $instance['text_placeholder'] : apply_filters( 'rstore_domain_text_placeholder', esc_html__( 'Find your perfect domain name', 'reseller-store' ) ),
			'text_search'        => isset( $instance['text_search'] ) ? $instance['text_search'] : apply_filters( 'rstore_domain_text_search', esc_html__( 'Search', 'reseller-store' ) ),
			'text_available'     => isset( $instance['text_available'] ) ? $instance['text_available'] : apply_filters( 'rstore_domain_text_available', esc_html__( 'Congrats, {domain_name} is available!', 'reseller-store' ) ),
			'text_not_available' => isset( $instance['text_not_available'] ) ? $instance['text_not_available'] : apply_filters( 'rstore_domain_text_not_available', esc_html__( 'Sorry, {domain_name} is taken.', 'reseller-store' ) ),
			'text_cart'          => isset( $instance['text_cart'] ) ? $instance['text_cart'] : apply_filters( 'rstore_domain_text_cart', esc_html__( 'Continue to cart', 'reseller-store' ) ),
			'text_select'        => isset( $instance['text_select'] ) ? $instance['text_select'] : apply_filters( 'rstore_text_select', esc_html__( 'Select', 'reseller-store' ) ),
			'text_selected'      => isset( $instance['text_selected'] ) ? $instance['text_selected'] : apply_filters( 'rstore_text_selected', esc_html__( 'Selected', 'reseller-store' ) ),
			'modal'              => isset( $instance['modal'] ) ? ! empty( $instance['modal'] ) : apply_filters( 'rstore_domain_modal', false ),
		);
	}

}
