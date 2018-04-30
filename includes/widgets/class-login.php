<?php
/**
 * GoDaddy Reseller Store login button widget class.
 *
 * Handles the Reseller store domain search widget.
 *
 * @class    Reseller_Store/Widgets/Login
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

final class Login extends \WP_Widget {

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		parent::__construct(
			rstore_prefix( 'login' ),
			esc_html__( 'Reseller Shopper Login', 'reseller-store' ),
			array(
				'classname'   => rstore_prefix( 'login', true ),
				'description' => esc_html__( 'A shopper login status', 'reseller-store' ),
				'category'    => __( 'Reseller Store Modules', 'reseller-store' ),
			)
		);

	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.1.0
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
		 * @since 1.1.0
		 *
		 * @var array
		 */
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_login_widget_classes', [] ) );

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
		<div class="rstore-login-block" style="display: block;">
			<!-- Show login button -->
			<a class="login-link" href="<?php echo rstore()->api->url( 'account' ); ?>" rel="nofollow"><?php echo $data['login_button_text']; ?></a>
		</div>

		<div  class="rstore-welcome-block" style="display: none;">
			<!--- Show welcome message -->
			<span class="welcome-message"><?php echo $data['welcome_message']; ?></span>
			<span class="firstname"></span>
			<span class="lastname"></span>
			<a class="logout-link" href="<?php echo rstore()->api->url( 'sso', 'logout' ); ?>" rel="nofollow"><?php echo $data['logout_button_text']; ?></a>
		</div>

		<?php

		echo $args['after_widget']; // xss ok.

		$login_widget = ob_get_contents();
		ob_get_clean();

		if ( Shortcodes::is_widget( $args ) ) {

			echo $login_widget;

		}

		return $login_widget;

	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @since 1.1.0
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {
		$data = $this->get_data( $instance );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $data['title'] ); ?>" class="widefat">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'login_button_text' ) ); ?>"><?php esc_html_e( 'Log In Button:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'login_button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'login_button_text' ) ); ?>" value="<?php echo esc_attr( $data['login_button_text'] ); ?>" class="widefat">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'logout_button_text' ) ); ?>"><?php esc_html_e( 'Log Out Button:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'logout_button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'logout_button_text' ) ); ?>" value="<?php echo esc_attr( $data['logout_button_text'] ); ?>" class="widefat">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'welcome_message' ) ); ?>"><?php esc_html_e( 'Welcome Message:', 'reseller' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'welcome_message' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'welcome_message' ) ); ?>" value="<?php echo esc_attr( $data['welcome_message'] ); ?>" class="widefat">
		</p>

		<?php

	}

	/**
	 * Processing widget options on save.
	 *
	 * @since 1.1.0
	 *
	 * @param  array $new_instance New widget instance.
	 * @param  array $old_instance Old widget instance.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance['title']              = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : null;
		$instance['welcome_message']    = isset( $new_instance['welcome_message'] ) ? wp_kses_post( $new_instance['welcome_message'] ) : null;
		$instance['login_button_text']  = isset( $new_instance['login_button_text'] ) ? wp_kses_post( $new_instance['login_button_text'] ) : null;
		$instance['logout_button_text'] = isset( $new_instance['logout_button_text'] ) ? wp_kses_post( $new_instance['logout_button_text'] ) : null;

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
			'welcome_message'    => isset( $instance['welcome_message'] ) ? $instance['welcome_message'] : esc_html__( 'Welcome Back', 'reseller-store' ),
			'login_button_text'  => isset( $instance['login_button_text'] ) ? $instance['login_button_text'] : esc_html__( 'Log In', 'reseller-store' ),
			'logout_button_text' => isset( $instance['logout_button_text'] ) ? $instance['logout_button_text'] : esc_html__( 'Log Out', 'reseller-store' ),
		);
	}

}
