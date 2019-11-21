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

final class Login extends Widget_Base {

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
				'description' => esc_html__( 'A shopper login status.', 'reseller-store' ),
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
	 *
	 * @return mixed Returns the HTML markup for the login container.
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
		$classes = array_map( 'sanitize_html_class', (array) apply_filters( 'rstore_login_widget_classes', array() ) );

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
			<a class="login-link" href="<?php echo esc_url_raw( rstore()->api->url( 'account' ), 'https' ); ?>" rel="nofollow"><?php echo esc_html( $data['login_button_text'] ); ?></a>
		</div>

		<div  class="rstore-welcome-block" style="display: none;">
			<!--- Show welcome message -->
			<span class="welcome-message"><?php echo esc_html( $data['welcome_message'] ); ?></span>
			<span class="firstname"></span>
			<span class="lastname"></span>
			<a class="logout-link" href="<?php echo esc_url_raw( rstore()->api->url( 'sso', 'logout' ), 'https' ); ?>" rel="nofollow"><?php echo esc_html( $data['logout_button_text'] ); ?></a>
		</div>

		<?php

		echo $args['after_widget']; // xss ok.

		$login_widget = ob_get_contents();
		ob_get_clean();

		$login_widget = apply_filters( 'rstore_login_html', $login_widget );

		if ( apply_filters( 'rstore_is_widget', $args ) ) {

			echo $login_widget; // xss ok.

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
		$this->display_form_input( 'title', $data['title'], __( 'Title', 'reseller-store' ) );
		$this->display_form_input( 'login_button_text', $data['login_button_text'], __( 'Sign In Button', 'reseller-store' ) );
		$this->display_form_input( 'welcome_message', $data['welcome_message'], __( 'Welcome Message', 'reseller-store' ) );
		$this->display_form_input( 'logout_button_text', $data['logout_button_text'], __( 'Log Out Button', 'reseller-store' ) );

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
			'title'              => isset( $instance['title'] ) ? $instance['title'] : apply_filters( 'rstore_login_title', '' ),
			'welcome_message'    => isset( $instance['welcome_message'] ) ? $instance['welcome_message'] : apply_filters( 'rstore_login_welcome_message', esc_html__( 'Welcome Back', 'reseller-store' ) ),
			'login_button_text'  => isset( $instance['login_button_text'] ) ? $instance['login_button_text'] : apply_filters( 'rstore_login_button_text', esc_html__( 'Sign In', 'reseller-store' ) ),
			'logout_button_text' => isset( $instance['logout_button_text'] ) ? $instance['logout_button_text'] : apply_filters( 'rstore_logout_button_text', esc_html__( 'Log Out', 'reseller-store' ) ),
		);
	}

}
