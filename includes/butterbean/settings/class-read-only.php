<?php
/**
 * GoDaddy Reseller Store read-only control class.
 *
 * This control renders the price on a product post.
 *
 * @class    Reseller_Store/ButterBean/Controls/Read_Only
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.1.1
 */

namespace Reseller_Store\ButterBean\Settings;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Read_Only extends \ButterBean_Setting {

	/**
	 * The type of control.
	 *
	 * @since  1.1.1
	 * @access public
	 * @var    string
	 */
	public $type = 'read-only';

	/**
	 * Creates a new control object.
	 *
	 * @since  1.1.1
	 * @access public
	 * @param  object $manager ButterBean_Manager instance.
	 * @param  string $name    Setting Name.
	 * @param  array  $args     ButterBean control attributes.
	 */
	public function __construct( $manager, $name, $args = array() ) {

		parent::__construct( $manager, $name, $args );

		$this->type = rstore_prefix( $this->type, true );

	}

	/**
	 * Saves the value of the setting.
	 *
	 * @since  1.1.1
	 * @access public
	 * @return void
	 */
	public function save() {}

}
