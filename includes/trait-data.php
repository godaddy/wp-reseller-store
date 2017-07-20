<?php
/**
 * GoDaddy Reseller Store Data.
 *
 * Reseller store product data trait.
 *
 * @trait    Reseller_Store/Data
 * @package  Reseller_Store/Plugin
 * @category trait
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

trait Data {

	/**
	 * Data object.
	 *
	 * @since 0.2.0
	 *
	 * @var object|bool
	 */
	protected $data = false;

	/**
	 * Magic data getter.
	 *
	 * @since 0.2.0
	 *
	 * @param string $key Object name to get.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {

		if ( 'data' === $key ) {

			return $this->data;

		}

		return isset( $this->data->{$key} ) ? $this->data->{$key} : false;

	}

	/**
	 * Magic data setter.
	 *
	 * @since 0.2.0
	 *
	 * @param string $key   Object name to set.
	 * @param mixed  $value Object value to set.
	 *
	 * @return mixed
	 */
	public function __set( $key, $value ) {

		if ( ! $this->data ) {

			$this->data = new \stdClass;

		}

		$this->data->{$key} = $value;

	}

}
