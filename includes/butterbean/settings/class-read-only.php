<?php

namespace Reseller_Store\ButterBean\Settings;

use Reseller_Store\Plugin;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Read_Only extends \ButterBean_Setting {

	public $type = 'read-only';

	public function __construct( $manager, $name, $args = [] ) {

		parent::__construct( $manager, $name, $args );

		$this->type = Plugin::prefix( $this->type, true );

	}

	public function save() {}

}
