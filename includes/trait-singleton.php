<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

trait Singleton {

	/**
	 * The plugin instance.
	 *
	 * @since NEXT
	 *
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Return the plugin instance.
	 *
	 * @since NEXT
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
	 * @since NEXT
	 */
	public static function reset() {

		static::$instance = null;

	}

}
