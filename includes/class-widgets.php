<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Widgets {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		echo "add_action( 'widgets_init'\n";

		add_action( 'widgets_init', [ get_called_class(), 'register_widgets' ] );

	}

	/**
	 * Register our custom widget using the api
	 */
	public static function register_widgets() {

		register_widget( __NAMESPACE__ . '\Widgets\Cart' );

		register_widget( __NAMESPACE__ . '\Widgets\Domain_Search' );

		register_widget( __NAMESPACE__ . '\Widgets\Product' );
	}

}
