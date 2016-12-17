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

	use Singleton, Data;

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
		new Post_Type;
		new Settings;
		new Taxonomies;
		new Widgets;

		/**
		 * Register custom WP-CLI command.
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {

			\WP_CLI::add_command( 'reseller', __NAMESPACE__ . '\CLI' );

		}

	}

	/**
	 * Return a plugin option value.
	 *
	 * @since NEXT
	 *
	 * @param  string $key
	 * @param  mixed  $default (optional)
	 *
	 * @return mixed
	 */
	public function get_option( $key, $default = false ) {

		return get_option( "rstore_{$key}", $default );

	}

	/**
	 * Return product meta value, or the global setting fallback.
	 *
	 * @since NEXT
	 *
	 * @param  int    $id
	 * @param  string $key
	 * @param  mixed  $default (optional)
	 *
	 * @return mixed
	 */
	public function get_product_meta( $id, $key, $default = false ) {

		return metadata_exists( 'post', $id, $key ) ? get_post_meta( $id, $key, true ) : $this->get_option( $key, $default );

	}

	/**
	 * Check if the plugin is setup.
	 *
	 * @since NEXT
	 *
	 * @return bool
	 */
	public function is_setup() {

		return ! empty( $this->get_option( 'id' ) );

	}

}

rstore();
