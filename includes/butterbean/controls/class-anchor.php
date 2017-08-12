<?php

namespace Reseller_Store\ButterBean\Controls;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Anchor extends \ButterBean_Control {

	public $type = 'anchor';

	public $text;

	public function __construct( $manager, $name, $args = array() ) {

		parent::__construct( $manager, $name, $args );

		$this->type = rstore_prefix( $this->type, true );

	}

	public function to_json() {

		parent::to_json();

		$this->json['text'] = ! empty( $this->text ) ? $this->text : '';

	}

}
