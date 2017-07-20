<?php
/**
 * Plugin Name: GoDaddy Reseller Store
 * Description: Sell hosting, domains, and more right from your WordPress site.
 * Version: 0.2.0
 * Author: GoDaddy
 * Author URI: https://reseller.godaddy.com/
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: godaddy-reseller-store
 * Domain Path: /languages
 *
 * This plugin, like WordPress, is licensed under the GPL.
 * Use it to make something cool, have fun, and share what you've learned with others.
 *
 * Copyright © 2017 GoDaddy Operating Company, LLC. All Rights Reserved.
 */

namespace Reseller_Store;

use stdClass;
use WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

require_once __DIR__ . '/includes/autoload.php';

final class Plugin {

	use Singleton, Data, Helpers;

	/**
	 * Plugin version.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	const VERSION = '0.2.0';

	/**
	 * Plugin prefix.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	const PREFIX = 'rstore_';

	/**
	 * Class contructor.
	 *
	 * @since 0.2.0
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
 * @since 0.2.0
 */
register_deactivation_hook( __FILE__, [ __NAMESPACE__ . '\Setup', 'deactivate' ] );
