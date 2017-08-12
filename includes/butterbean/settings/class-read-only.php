<?php

namespace Reseller_Store\ButterBean\Settings;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Read_Only extends \ButterBean_Setting {

	public $type = 'read-only';

	public function __construct( $manager, $name, $args = array() ) {

		parent::__construct( $manager, $name, $args );

		$this->type = rstore_prefix( $this->type, true );

	}

	public function save() {}

}
