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

if ( ! defined( 'ABSPATH' ) ) {

	/**
	* @codeCoverageIgnore
	*/
	exit;

}

final class Domain_Search extends \WP_Widget {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		parent::__construct(
			rstore_prefix( 'domain' ),
			esc_html__( 'Reseller Domain Search', 'reseller-store' ),
			[
				'classname'   => rstore_prefix( 'domain', true ),
				'description' => esc_html__( 'A search form for domain names.', 'reseller-store' ),
			]
		);

	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 0.2.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
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

		echo $args['before_widget']; // xss ok.

		if ( ! empty( $instance['title'] ) ) {

			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; // xss ok.

		}

		$data = $this->get_data( $instance );

		$domain_html = '<div class="rstore-domain-search" data-plid=' . rstore_get_option( 'pl_id' );

		foreach ( $data as $key => $text ) {
			if ( ! empty( $text ) ) {
				$domain_html .= ' data-' . $key . '="' . $text . '"';
			}
		}

		$domain_html .= '></div>';

		echo apply_filters( 'rstore_domain_html', $domain_html );

		echo $args['after_widget']; // xss ok.

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
		$this->display_form_input( 'page-size', $data['page-size'], esc_html_e( 'Domain result page size:', 'reseller' ), 'number' );
		$this->display_form_input( 'text-placeholder', $data['text-placeholder'], esc_html_e( 'Placeholder:', 'reseller' ) );
		$this->display_form_input( 'text-search', $data['text-search'], esc_html_e( 'Search Button:', 'reseller' ) );
		$this->display_form_input( 'text-available', $data['text-available'], esc_html_e( 'Available Text:', 'reseller' ) );
		$this->display_form_input( 'text-not-available', $data['text-not-available'], esc_html_e( 'Not Available Text:', 'reseller' ) );
		$this->display_form_input( 'text-cart', $data['text-cart'], esc_html_e( 'Cart Button Text:', 'reseller' ) );
		$this->display_form_input( 'text-select', $data['text-select'], esc_html_e( 'Select Button Text:', 'reseller' ) );
		$this->display_form_input( 'text-selected', $data['text-selected'], esc_html_e( 'Unselect Button Text:', 'reseller' ) );
	}

	/**
	 * Display form input field
	 *
	 * @since NEXT
	 *
	 * @param  string $field Feield name.
	 * @param  array  $value Value of the field.
	 * @param  array  $label Form label text.
	 * @param  string $type (optional) Input type label text.
	 */
	private function display_form_input( $field, $value, $label, $type = 'text' ) {
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>"><?php $label; ?></label>
			<input type="<?php echo $type; ?>" id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" value="<?php echo esc_attr( $value ); ?>" class="widefat">
		</p>
		<?php
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
		$instance['page-size']        = isset( $new_instance['page-size'] ) ? sanitize_text_field( $new_instance['page-size'] ) : null;
		$instance['text-placeholder']  = isset( $new_instance['text-placeholder'] ) ? wp_kses_post( $new_instance['text-placeholder'] ) : null;
		$instance['text-search']  = isset( $new_instance['text-search'] ) ? wp_kses_post( $new_instance['text-search'] ) : null;
		$instance['text-available']  = isset( $new_instance['text-available'] ) ? wp_kses_post( $new_instance['text-available'] ) : null;
		$instance['text-not-available']  = isset( $new_instance['text-not-available'] ) ? wp_kses_post( $new_instance['text-not-available'] ) : null;
		$instance['text-cart']  = isset( $new_instance['text-cart'] ) ? wp_kses_post( $new_instance['text-cart'] ) : null;
		$instance['text-select']  = isset( $new_instance['text-select'] ) ? wp_kses_post( $new_instance['text-select'] ) : null;
		$instance['text-selected']  = isset( $new_instance['text-selected'] ) ? wp_kses_post( $new_instance['text-selected'] ) : null;

		return $instance;

	}

	/**
	 * Set data from instance or default value.
	 *
	 * @since NEXT
	 *
	 * @param  array $instance Widget instance.
	 *
	 * @return array
	 */
	private function get_data( $instance ) {
		return array(
			'title'           => isset( $instance['title'] ) ? $instance['title'] : '',
			'page-size'     => isset( $instance['page-size'] ) ? $instance['page-size'] : 5,
			'text-placeholder'     => isset( $instance['text-placeholder'] ) ? $instance['text-placeholder'] : esc_html__( 'Find your perfect domain name', 'reseller-store' ),
			'text-search'          => isset( $instance['text-search'] ) ? $instance['text-search'] : esc_html__( 'Search', 'reseller-store' ),
			'text-available'       => isset( $instance['text-available'] ) ? $instance['text-available'] : esc_html__( 'Congrats, your domain is available!', 'reseller-store' ),
			'text-not-available'   => isset( $instance['text-not-available'] ) ? $instance['text-not-available'] : esc_html__( 'Sorry that domain is taken', 'reseller-store' ),
			'text-cart'            => isset( $instance['text-cart'] ) ? $instance['text-cart'] : esc_html__( 'Continue to Cart', 'reseller-store' ),
			'text-select'   => isset( $instance['text-select'] ) ? $instance['text-select'] : esc_html__( 'Select', 'reseller-store' ),
			'text-selected' => isset( $instance['text-selected'] ) ? $instance['text-selected'] : esc_html__( 'Selected', 'reseller-store' ),
		);
	}

}
