<?php
/**
 * GoDaddy Reseller Store Autoloader class.
 *
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

foreach ( glob( __DIR__ . '/functions/*.php' ) as $include ) {

	if ( is_readable( $include ) ) {

		require_once $include;

	}
}

spl_autoload_register(
	function( $resource ) {

		if ( 0 !== strpos( $resource, __NAMESPACE__ ) ) {

			return;

		}

		$resource = strtolower(
			str_replace(
				array( __NAMESPACE__ . '\\', '_' ),
				array( '', '-' ),
				$resource
			)
		);

		$parts = explode( '\\', $resource );
		$name  = array_pop( $parts );
		$files = str_replace( '//', '/', glob( sprintf( '%s/%s/*-%s.php', __DIR__, implode( '/', $parts ), $name ) ) );

		if ( isset( $files[0] ) && is_readable( $files[0] ) ) {
			require_once $files[0];

		}

	}
);
