<?php
/**
 * GoDaddy Reseller Store Singleton.
 *
 * Reseller store product singleton trait. Ensures only one instance of the
 * GoDaddy Reseller Store plugin is instantiated at any given time.
 *
 * @trait    Reseller_Store/Singleton
 * @package  Reseller_Store/Plugin
 * @category trait
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

trait Singleton {

	/**
	 * The plugin instance.
	 *
	 * @since 0.2.0
	 *
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Return the plugin instance.
	 *
	 * @since 0.2.0
	 *
	 * @return Plugin
	 */
	public static function load() {

		if ( ! static::$instance ) {

			static::$instance = new self();

		}

		return static::$instance;

	}

	/**
	 * Reset the plugin instance.
	 *
	 * @since 0.2.0
	 */
	public static function reset() {

		static::$instance = null;

	}

}
