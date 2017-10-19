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

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
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

		$domain_html .= '>' . esc_html__( 'Domain Search', 'reseller' ) . '</div>';

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
		$this->display_form_input( 'page_size', $data['page_size'], esc_html_e( 'Domain result page size:', 'reseller' ), 'number' );
		$this->display_form_input( 'text_placeholder', $data['text_placeholder'], esc_html_e( 'Placeholder:', 'reseller' ) );
		$this->display_form_input( 'text_search', $data['text_search'], esc_html_e( 'Search Button:', 'reseller' ) );
		$this->display_form_input( 'text_available', $data['text_available'], esc_html_e( 'Available Text:', 'reseller' ) );
		$this->display_form_input( 'text_not_available', $data['text_not_available'], esc_html_e( 'Not Available Text:', 'reseller' ) );
		$this->display_form_input( 'text_cart', $data['text_cart'], esc_html_e( 'Cart Button Text:', 'reseller' ) );
		$this->display_form_input( 'text_select', $data['text_select'], esc_html_e( 'Select Button Text:', 'reseller' ) );
		$this->display_form_input( 'text_selected', $data['text_selected'], esc_html_e( 'Unselect Button Text:', 'reseller' ) );
		$this->display_form_input( 'text_verify', $data['text_verify'], esc_html_e( 'Verify Button Text:', 'reseller' ) );
		$this->display_form_input( 'text_disclaimer', $data['text_disclaimer'], esc_html_e( 'Domain disclaimer notice', 'reseller' ) );
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
		$instance['page_size']        = isset( $new_instance['page_size'] ) ? sanitize_text_field( $new_instance['page_size'] ) : null;
		$instance['text_placeholder']  = isset( $new_instance['text_placeholder'] ) ? wp_kses_post( $new_instance['text_placeholder'] ) : null;
		$instance['text_search']  = isset( $new_instance['text_search'] ) ? wp_kses_post( $new_instance['text_search'] ) : null;
		$instance['text_available']  = isset( $new_instance['text_available'] ) ? wp_kses_post( $new_instance['text_available'] ) : null;
		$instance['text_not_available']  = isset( $new_instance['text_not_available'] ) ? wp_kses_post( $new_instance['text_not_available'] ) : null;
		$instance['text_cart']  = isset( $new_instance['text_cart'] ) ? wp_kses_post( $new_instance['text_cart'] ) : null;
		$instance['text_select']  = isset( $new_instance['text_select'] ) ? wp_kses_post( $new_instance['text_select'] ) : null;
		$instance['text_selected']  = isset( $new_instance['text_selected'] ) ? wp_kses_post( $new_instance['text_selected'] ) : null;
		$instance['text_verify']  = isset( $new_instance['text_verify'] ) ? wp_kses_post( $new_instance['text_verify'] ) : null;
		$instance['text_disclaimer']  = isset( $new_instance['text_disclaimer'] ) ? wp_kses_post( $new_instance['text_disclaimer'] ) : null;

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
			'page_size'     => isset( $instance['page_size'] ) ? $instance['page_size'] : 5,
			'text_placeholder'     => isset( $instance['text_placeholder'] ) ? $instance['text_placeholder'] : esc_html__( 'Find your perfect domain name', 'reseller-store' ),
			'text_search'          => isset( $instance['text_search'] ) ? $instance['text_search'] : esc_html__( 'Search', 'reseller-store' ),
			'text_available'       => isset( $instance['text_available'] ) ? $instance['text_available'] : esc_html__( 'Congrats, your domain is available!', 'reseller-store' ),
			'text_not_available'   => isset( $instance['text_not_available'] ) ? $instance['text_not_available'] : esc_html__( 'Sorry that domain is taken', 'reseller-store' ),
			'text_cart'            => isset( $instance['text_cart'] ) ? $instance['text_cart'] : esc_html__( 'Continue to cart', 'reseller-store' ),
			'text_select'   => isset( $instance['text_select'] ) ? $instance['text_select'] : esc_html__( 'Select', 'reseller-store' ),
			'text_selected' => isset( $instance['text_selected'] ) ? $instance['text_selected'] : esc_html__( 'Selected', 'reseller-store' ),
			'text_verify' => isset( $instance['text_verify'] ) ? $instance['text_verify'] : esc_html__( 'Verify', 'reseller-store' ),
			'text_disclaimer' => isset( $instance['text_disclaimer'] ) ? $instance['text_disclaimer'] : '',
		);
	}

}
