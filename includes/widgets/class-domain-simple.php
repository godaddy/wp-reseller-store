<?php
/**
 * GoDaddy Reseller Store domain simple widget class.
 *
 * Handles the Reseller store domain simple widget.
 *
 * @class    Reseller_Store/Widgets/Domain_Simple
 * @package  WP_Widget
 * @category Class
 * @author   GoDaddy
 * @since    1.6.0
 */

namespace Reseller_Store\Widgets;

use Reseller_Store\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Domain_Simple extends Widget_Base {

	/**
	 * Class constructor.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		parent::__construct(
			rstore_prefix( 'domain_simple' ),
			esc_html__( 'Reseller Domain Search', 'reseller-store' ),
			array(
				'classname'   => rstore_prefix( 'domain', true ),
				'description' => esc_html__( 'A search form for domain registrations.', 'reseller-store' ),
				'group'       => __( 'Reseller Store Modules', 'reseller-store' ),
			)
		);

	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.6.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 *
	 * @return mixed Returns the HTML markup for the domain transfer container.
	 */
	public function widget( $args, $instance ) {

		/**
		 * Filter classes to be appended to the Domain Simple widget.
		 *
		 * The `widget_search` class is added here to be sure our
		 * Domain Search widget inherits any default Search widget
		 * styles included by a theme.
		 *
		 * @since 1.6.0
		 *
		 * @var array
		 */
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_domain_widget_classes', array( 'widget_search' ) ) );

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
		<form role="search" method="get" class="search-form" action="<?php echo esc_url_raw( rstore()->api->url( 'www', 'products/domain-registration/find' ), 'https' ); ?>">
			<label>
				<input type="search" class="search-field" placeholder="<?php echo esc_attr( $data['text_placeholder'] ); ?>" name="domainToCheck" required>
			</label>
			<input type="hidden" class="hidden" value="<?php echo esc_attr( rstore_get_option( 'pl_id' ) ); ?>" name="plid">
			<input type="hidden" class="hidden" value="slp_rstore" name="itc">
			<input type="submit" class="search-submit" value="<?php echo esc_attr( $data['text_search'] ); ?>">
		</form>
		<?php

		echo $args['after_widget']; // xss ok.

		$domain_search_widget = ob_get_contents();
		ob_get_clean();

		$domain_search_widget = apply_filters( 'rstore_domain_html', $domain_search_widget );

		if ( apply_filters( 'rstore_is_widget', $args ) ) {

			echo $domain_search_widget; // xss ok.

		}

		return $domain_search_widget;

	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @since 1.6.0
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {
		$data = $this->get_data( $instance );
		$this->display_form_input( 'title', $data['title'], __( 'Title', 'reseller-store' ) );
		$this->display_form_input( 'text_placeholder', $data['text_placeholder'], __( 'Placeholder', 'reseller-store' ) );
		$this->display_form_input( 'text_search', $data['text_search'], __( 'Button', 'reseller-store' ) );
	}

	/**
	 * Processing widget options on save.
	 *
	 * @since 1.6.0
	 *
	 * @param  array $new_instance New widget instance.
	 * @param  array $old_instance Old widget instance.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance['title']            = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : null;
		$instance['text_placeholder'] = isset( $new_instance['text_placeholder'] ) ? wp_kses_post( $new_instance['text_placeholder'] ) : null;
		$instance['text_search']      = isset( $new_instance['text_search'] ) ? wp_kses_post( $new_instance['text_search'] ) : null;

		return $instance;

	}

	/**
	 * Set data from instance or default value.
	 *
	 * @since 1.6.0
	 *
	 * @param  array $instance Widget instance.
	 *
	 * @return array
	 */
	private function get_data( $instance ) {
		return array(
			'title'            => isset( $instance['title'] ) ? $instance['title'] : apply_filters( 'rstore_domain_title', '' ),
			'text_placeholder' => isset( $instance['text_placeholder'] ) ? $instance['text_placeholder'] : apply_filters( 'rstore_domain_text_placeholder', esc_html__( 'Find your perfect domain name', 'reseller-store' ) ),
			'text_search'      => isset( $instance['text_search'] ) ? $instance['text_search'] : apply_filters( 'rstore_domain_text_search', esc_html__( 'Search', 'reseller-store' ) ),
		);
	}

}
