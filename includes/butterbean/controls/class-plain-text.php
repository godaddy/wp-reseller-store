<?php
/**
 * GoDaddy Reseller Store plain-text control class.
 *
 * This control renders a label on a product post edit.
 *
 * @class    Reseller_Store/ButterBean/Controls/Plain_Text
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.1.1
 */

namespace Reseller_Store\ButterBean\Controls;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Plain_Text extends \ButterBean_Control {

	/**
	 * The type of control.
	 *
	 * @since  1.1.1
	 * @access public
	 * @var    string
	 */
	public $type = 'plain-text';

	/**
	 * The default value to display.
	 *
	 * @since  1.1.1
	 * @access public
	 * @var    string
	 */
	public $default;

	/**
	 * Creates a new control object.
	 *
	 * @since 1.1.1
	 * @access public
	 * @param object $manager ButterBean_Manager instance.
	 * @param string $name    Setting Name.
	 * @param array  $args     ButterBean control attributes.
	 */
	public function __construct( $manager, $name, $args = array() ) {

		parent::__construct( $manager, $name, $args );

		$this->type = rstore_prefix( $this->type, true );

	}

	/**
	 * Adds custom data to the json array. This data is passed to the Underscore template.
	 *
	 * @since  1.1.1
	 * @access public
	 * @return void
	 */
	public function to_json() {

		parent::to_json();

		$value = $this->get_value();

		$this->json['value'] = ( $value ) ? $value : ( ! empty( $this->default ) ? $this->default : '' );

	}

}
