<?php
/**
 * Plugin Name: Reseller Store
 * Description: Sell hosting, domains, and more right from your WordPress site.
 * Version: 2.2.16
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
 * Copyright © 2019 GoDaddy Operating Company, LLC. All Rights Reserved.
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

if ( version_compare( phpversion(), '5.4.0', '<' ) ) {

	add_action(
		'admin_notices',
		function() {
			?>
		<div class="update-nag">
			<?php
			echo sprintf(
				/* translators: server PHP version */
				esc_html__( 'You need to update your PHP version to run GoDaddy Reseller Store plugin. Required version 5.4 or higher. Your PHP version is: %s', 'reseller-store' ),
				esc_html( phpversion() )
			);

			?>
		</div>
			<?php
		}
	);

} else {

	require_once __DIR__ . '/includes/autoload.php';
	require_once __DIR__ . '/class-plugin.php';

	rstore();

	/**
	 * Register deactivation hook.
	 *
	 * @since 0.2.0
	 */
	register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Setup', 'deactivate' ) );
}
