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

		$post = Tests\Helper::create_product();

		$instance = [
			'title'    => '',
			'placeholder' => '',
			'button_label' => '',
		];

		$args = [
			'before_widget' => '<div class="before_widget">',
			'after_widget' => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		];

		$widget->widget( $args, $instance );

		// display domain search.
		$this->expectOutputRegex( '/<div class="rstore-domain-search"><\/div>/' );

	}

	/**
	 * @testdox Given a new instance the instance should update
	 */
	function test_widget_update() {

		$widget = new Widgets\Domain_Search();

		$old_instance = [
			'title'    => '',
			'placeholder' => '',
			'button_label' => '',
		];

		$new_instance = [
			'title'    => 'title 1',
			'placeholder' => 'placeholder 1',
			'button_label' => 'button_label 1',
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
			'button_label' => 'button_label',
		];

		$widget->form( $instance );

		foreach ( $instance as $key => $value ) {
			$this->expectOutputRegex( '/<input type="text" id="widget-rstore_domain-search--' . $key . '" name="widget-rstore_domain-search\[\]\[' . $key . '\]" value="' . $value . '" class="widefat">/' );
		}

	}


}
