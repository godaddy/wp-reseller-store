<?php

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

class ButterBean_Setting_Read_Only extends \ButterBean_Setting {

	public $type = 'read_only';

	public function save() {}

}
