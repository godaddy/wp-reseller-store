<?php

namespace Reseller_Store\ButterBean\Controls;

use Reseller_Store\Plugin;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Anchor extends \ButterBean_Control {

	public $type = 'anchor';

	public $text;

	public function __construct( $manager, $name, $args = [] ) {

		parent::__construct( $manager, $name, $args );

		$this->type = Plugin::prefix( $this->type, true );

	}

	public function to_json() {

		parent::to_json();

		$this->json['text'] = ! empty( $this->text ) ? $this->text : '';

	}

}
