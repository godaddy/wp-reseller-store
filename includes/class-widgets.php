<?php
/**
 * GoDaddy Reseller Store widgets class.
 *
 * Handles the Reseller store widgets.
 *
 * @class    Reseller_Store/Widgets
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Widgets {


	/**
	 * Module slugs.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	private $modules = array(
		'domain-simple',
		'domain-transfer',
		'domain-search',
		'product',
		'login',
		'cart',
	);

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// load beaver builder modules.
		add_action( 'init', array( $this, 'load_fl_modules' ) );

		// load visual composer modules.
		add_action( 'vc_before_init', array( $this, 'load_vc_modules' ) );
	}

	/**
	 * Register our custom widget using the api
	 */
	public function register_widgets() {

		register_widget( __NAMESPACE__ . '\Widgets\Cart' );

		register_widget( __NAMESPACE__ . '\Widgets\Domain_Search' );

		register_widget( __NAMESPACE__ . '\Widgets\Domain_Simple' );

		register_widget( __NAMESPACE__ . '\Widgets\Domain_Transfer' );

		register_widget( __NAMESPACE__ . '\Widgets\Login' );

		register_widget( __NAMESPACE__ . '\Widgets\Product' );
	}

	/**
	 * Loads the builder modules if the class exists.
	 *
	 * @action init
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function load_fl_modules() {

		if ( ! class_exists( 'FLBuilder' ) ) {
			return;
		}

		foreach ( $this->modules as $slug ) {

			$path = __DIR__ . "/modules/rstore-fl-{$slug}.php";

			if ( is_readable( $path ) ) {

				require_once $path;

			}
		}

	}

	/**
	 * Loads the Visual Composer if the VC installed.
	 *
	 * @action vc_before_init
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function load_vc_modules() {

		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		foreach ( $this->modules as $slug ) {

			$path = __DIR__ . "/modules/rstore-vc-{$slug}.php";

			if ( is_readable( $path ) ) {

				require_once $path;

			}
		}
	}

}
