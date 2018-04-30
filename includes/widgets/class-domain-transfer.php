<?php
/**
 * GoDaddy Reseller Store domain transfer widget class.
 *
 * Handles the Reseller store domain search widget.
 *
 * @class    Reseller_Store/Widgets/Domain_Transfer
 * @package  WP_Widget
 * @category Class
 * @author   GoDaddy
 * @since    NEXT
 */

namespace Reseller_Store\Widgets;

use Reseller_Store\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Domain_Transfer extends Widget_Base {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		parent::__construct(
			rstore_prefix( 'transfer' ),
			esc_html__( 'Reseller Domain Transfer', 'reseller-store' ),
			array(
				'classname'   => rstore_prefix( 'transfer', true ),
				'description' => esc_html__( 'A search form for domain transfers', '' ),
				'group'    => __( 'Reseller Store Modules', 'reseller-store' ),
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
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_domain_transfer_widget_classes', [ 'widget_search' ] ) );

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

		if ( ! empty( $instance['title'] ) ) {

			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; // xss ok.

		}

		$data = $this->get_data( $instance );
        ?>
		 <form role="search" method="get" class="search-form" action="<?php echo rstore()->api->url( 'www', 'products/domain-transfer' ) ?>">
				<label>
					<span class="screen-reader-text">Search for:</span>
					<input type="search" class="search-field" placeholder="<?php echo $data['text_placeholder']; ?>" value="" name="domainToCheck">
				</label>
                <input type="hidden" class="hidden" value="<?php echo rstore_get_option( 'pl_id' ); ?>" name="plid">
				<input type="submit" class="search-submit" value="<?php echo $data['text_button']; ?>">
			</form>
        <?php

		echo $args['after_widget']; // xss ok.

		$domain_search_widget = ob_get_contents();
		ob_get_clean();

		$domain_search_widget = apply_filters( 'rstore_transfer_html', $domain_search_widget );

		if ( Shortcodes::is_widget( $args ) ) {

			echo $domain_search_widget;

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
		$this->display_form_input( 'title', $data['title'], esc_html_e( 'Title:', 'reseller' ) );
		$this->display_form_input( 'text_placeholder', $data['text_placeholder'], esc_html_e( 'Placeholder:', 'reseller' ) );
		$this->display_form_input( 'text_button', $data['text_button'], esc_html_e( 'Transfer Button:', 'reseller' ) );
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
		$instance['text_placeholder']   = isset( $new_instance['text_placeholder'] ) ? wp_kses_post( $new_instance['text_placeholder'] ) : null;
		$instance['text_button']        = isset( $new_instance['text_button'] ) ? wp_kses_post( $new_instance['text_button'] ) : null;

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
			'title'              => isset( $instance['title'] ) ? $instance['title'] : '',
			'text_placeholder'   => isset( $instance['text_placeholder'] ) ? $instance['text_placeholder'] : esc_html__( 'Enter domain to transfer', 'reseller-store' ),
			'text_button'        => isset( $instance['text_button'] ) ? $instance['text_button'] : esc_html__( 'Transfer', 'reseller-store' ),
		);
	}

}
