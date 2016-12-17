<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Widgets {

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {

		add_action( 'widgets_init', function() {

			register_widget( __NAMESPACE__ . '\Widgets\Cart' );

			register_widget( __NAMESPACE__ . '\Widgets\Domain_Search' );

		} );

	}

}
