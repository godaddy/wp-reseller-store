<?php
/**
 * GoDaddy Reseller Store plugin class.
 *
 * Main loader for the plugin
 *
 * @class    Reseller_Store/Plugin
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.5.0
 */

namespace Reseller_Store;
use stdClass;
use WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Plugin {

	use Singleton, Data, Helpers;

	/**
	 * Plugin version.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	const VERSION = '2.2.16';

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

		add_action(
			'plugins_loaded',
			function () {

				load_plugin_textdomain( 'reseller-store', false, dirname( $this->basename ) . '/languages' );

			}
		);

		if ( defined( 'WP_CLI' ) && WP_CLI ) {

			WP_CLI::add_command( 'reseller', __NAMESPACE__ . '\CLI\Reseller' );

			WP_CLI::add_command( 'reseller product', __NAMESPACE__ . '\CLI\Reseller_Product' );

		}

		new Restrictions;
		new Post_Type;
		new Taxonomy_Category;
		new Taxonomy_Tag;
		new Settings;

		if ( ! rstore_is_setup() || ! rstore_has_products() ) {

			new Setup;

			return; // Bail until Setup is complete.

		}

		new Admin_Notices;
		new ButterBean;
		new Display;
		new Embed;
		new Permalinks;
		new Sync;
		new Widgets;
		new Shortcodes;
		new Blocks;
		new Bulk_Restore;
	}

}
