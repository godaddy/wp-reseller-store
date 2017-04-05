<?php

namespace Reseller_Store;

use stdClass;
use WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

require_once __DIR__ . '/autoload.php';

final class Plugin {

	use Singleton, Data, Helpers;

	/**
	 * Plugin version.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	const VERSION = '0.1.0';

	/**
	 * Plugin prefix.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	const PREFIX = 'rstore_';

	/**
	 * Class contructor.
	 *
	 * @since NEXT
	 */
	private function __construct() {

		$this->version    = self::VERSION;
		$this->basename   = plugin_basename( __FILE__ );
		$this->base_dir   = plugin_dir_path( __FILE__ );
		$this->assets_url = plugin_dir_url( __FILE__ ) . 'assets/';
		$this->api        = new API;

		add_action( 'plugins_loaded', function () {

			load_plugin_textdomain( 'reseller-store', false, dirname( $this->basename ) . '/languages' );

		} );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {

			WP_CLI::add_command( 'reseller', __NAMESPACE__ . '\CLI\Reseller' );

			WP_CLI::add_command( 'reseller product', __NAMESPACE__ . '\CLI\Reseller_Product' );

		}

		new Restrictions;

		if ( ! rstore_is_setup() || ! rstore_has_products() ) {

			new Setup;

			return; // Bail until Setup is complete.

		}

		new ButterBean;
		new Display;
		new Embed;
		new Permalinks;
		new Post_Type;
		new Sync;
		new Taxonomy_Category;
		new Taxonomy_Tag;
		new Widgets;

	}

}

rstore();

/**
 * Register deactivation hook.
 *
 * @since NEXT
 */
register_deactivation_hook( __FILE__, [ __NAMESPACE__ . '\Setup', 'deactivate' ] );

/**
 * Register uninstall hook.
 *
 * @since NEXT
 */
register_uninstall_hook( __FILE__, [ __NAMESPACE__ . '\Setup', 'uninstall' ] );
