<?php

namespace Reseller_Store\ButterBean\Controls;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Plain_Text extends \ButterBean_Control {

	public $type = 'plain-text';

	public $default;

	public function __construct( $manager, $name, $args = [] ) {

		parent::__construct( $manager, $name, $args );

		$this->type = rstore_prefix( $this->type, true );

	}

	public function to_json() {

		parent::to_json();

		$value = $this->get_value();

		$this->json['value'] = ( $value ) ? $value : ( ! empty( $this->default ) ? $this->default : '' );

	}

}
