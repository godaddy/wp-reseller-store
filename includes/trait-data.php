<?php

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
	 * Magic data getta.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $key
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
	 * Magic data setta.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $key
	 * @param  mixed  $value
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
