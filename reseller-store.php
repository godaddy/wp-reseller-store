<?php
/**
 * Plugin Name: GoDaddy Reseller Store
 * Description: A boilerplate WordPress plugin by GoDaddy.
 * Version: 0.1.0
 * Author: GoDaddy
 * Author URI: https://reseller.godaddy.com/
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: reseller-store
 * Domain Path: /languages
 *
 * This plugin, like WordPress, is licensed under the GPL.
 * Use it to make something cool, have fun, and share what you've learned with others.
 *
 * Copyright © 2016 GoDaddy Operating Company, LLC. All Rights Reserved.
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

require_once __DIR__ . '/includes/autoload.php';

final class Plugin {

	use Singleton, Data, Helpers;

	/**
	 * Class contructor.
	 *
	 * @since NEXT
	 */
	private function __construct() {

		$this->version    = '0.1.0';
		$this->basename   = plugin_basename( __FILE__ );
		$this->base_dir   = plugin_dir_path( __FILE__ );
		$this->assets_url = plugin_dir_url( __FILE__ ) . 'assets/';
		$this->api        = new API;

		/**
		 * Load languages.
		 */
		add_action( 'plugins_loaded', function() {

			load_plugin_textdomain( 'reseller-store', false, dirname( __FILE__ ) . '/languages' );

		} );

		if ( ! $this->is_setup() ) {

			new Setup;

			return; // Bail until Setup is complete

		}

		new Embed;
		new Product;
		new Product_Caps;
		new Product_Category;
		new Product_Tag;
		new Settings;
		new Widgets;

		/**
		 * Register custom WP-CLI command.
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {

			\WP_CLI::add_command( 'reseller', __NAMESPACE__ . '\CLI' );

		}

	}

}

rstore();
