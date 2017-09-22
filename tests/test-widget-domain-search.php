<?php
/**
 * GoDaddy Reseller Store Domain Search Widget tests
 */

namespace Reseller_Store;

final class TestWidgetDomainSearch extends TestCase {

	/**
	 * @testdox Test DomainSearch widgets exist.
	 */
	function test_basics() {

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Widgets\Domain_Search' ),
			'Class \Widgets\Domain_Search is not found'
		);

	}

	/**
	 * @testdox Given a valid instance the widget should render
	 */
	function test_widget() {

		$widget = new Widgets\Domain_Search();
		rstore_update_option( 'pl_id', 12345 );

		$instance = [
			'title'    => '',
			'placeholder' => '',
			'search' => '',
			'available'    => '',
			'not-available' => '',
			'cart' => '',
			'select'    => '',
			'selected' => '',
		];

		$args = [
			'before_widget' => '<div class="before_widget">',
			'after_widget' => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		];

		$widget->widget( $args, $instance );

		// display domain search.
		$this->expectOutputRegex( '/div class="rstore-domain-search" data-plid=12345><\/div>/' );

	}

	/**
	 * @testdox Given a new instance the instance should update
	 */
	function test_widget_update() {

		$widget = new Widgets\Domain_Search();

		$old_instance = [];

		$new_instance = [
			'title'    => 'title',
			'placeholder' => 'placeholder',
			'search' => 'search',
			'available'    => 'available',
			'not-available' => 'not-available',
			'cart' => 'cart',
			'select'    => 'select',
			'selected' => 'selected',
		];

		$instance = $widget->update( $new_instance, $old_instance );

		foreach ( $instance as $key => $value ) {
			$this->assertEquals( $instance[ $key ],  $new_instance[ $key ] );
		}

	}

	/**
	 * @testdox Given an instance the form should render
	 */
	function test_widget_form() {

		$widget = new Widgets\Domain_Search();

		$instance = [
			'title'    => 'title',
			'placeholder' => 'placeholder',
			'search' => 'search',
			'available'    => 'available',
			'not-available' => 'not-available',
			'cart' => 'cart',
			'select'    => 'select',
			'selected' => 'selected',
		];

		$widget->form( $instance );

		foreach ( $instance as $key => $value ) {
			$this->expectOutputRegex( '/<input type="text" id="widget-rstore_domain--' . $key . '" name="widget-rstore_domain\[\]\[' . $key . '\]" value="' . $value . '" class="widefat">/' );
		}

	}


}
