<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

trait Helpers {

	public static function base_dir( $path = '' ) {

		return rstore()->base_dir . $path;

	}

	public static function assets_url( $path = '' ) {

		return rstore()->assets_url . $path;

	}

}
