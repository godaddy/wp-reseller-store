<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Reseller_Store
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {

	$_tests_dir = '/tmp/wordpress-tests-lib';

}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {

	require dirname( dirname( __FILE__ ) ) . '/godaddy-reseller-store.php';

}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
require __DIR__ . '/class-helper.php';
require __DIR__ . '/testcase.php';
