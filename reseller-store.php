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

		$this->version    = '0.1.0';
		$this->basename   = plugin_basename( __FILE__ );
		$this->base_dir   = plugin_dir_path( __FILE__ );
		$this->assets_url = plugin_dir_url( __FILE__ ) . 'assets/';
		$this->api        = new API;

		add_action( 'plugins_loaded', function() {

			load_plugin_textdomain( 'reseller-store', false, dirname( __FILE__ ) . '/languages' );

		} );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {

			\WP_CLI::add_command( 'reseller', __NAMESPACE__ . '\CLI' );

		}

		new Restrictions;
		new Setup;

		if ( ! self::is_setup() ) {

			return; // Bail until Setup is complete

		}

		new Embed;
		new Post_Type;
		new Settings;
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
