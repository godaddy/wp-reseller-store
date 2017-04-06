<?php
/**
 * Plugin Name: GoDaddy Reseller Store
 * Description: Resell hosting, domains, and more right from your WordPress site.
 * Version: 0.2.0
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
 * Copyright © 2017 GoDaddy Operating Company, LLC. All Rights Reserved.
 */

/**
* Reseller plugin file base path
*
* @since NEXT
*
* @var string
*/
if ( ! defined( 'RSTORE_BASENAME' ) ) {

	define( 'RSTORE_BASENAME', plugin_basename( __FILE__ ) );

}

if ( version_compare( '5.4.0', phpversion(), '>' ) ) {

	include_once( dirname( __FILE__ ) . '/includes/functions/deactivate-functions.php' );

	return;

}

include_once( __DIR__ . '/includes/class-plugin.php' );
