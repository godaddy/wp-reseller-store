<?php
/**
 * GoDaddy Reseller Store Domain Transfer Widget tests
 */

namespace Reseller_Store;

final class TestWidgetDomainTransfer extends TestCase {

	/**
	 * @testdox Test Cart widgets exist.
	 */
	function test_basics() {

		$this->assertTrue(
			class_exists( __NAMESPACE__ . '\Widgets\Domain_Transfer' ),
			'Class \Widgets\Domain_Transfer is not found'
		);

	}

	/**
	 * @testdox Given a valid instance the widget should render
	 */
	function test_widget() {

		$widget = new Widgets\Domain_Transfer();

		rstore_update_option( 'pl_id', 12345 );

		$post = Tests\Helper::create_product();

		$instance = array(
			'post_id'          => $post->ID,
			'title'            => 'title',
			'text_placeholder' => 'transfer your domain',
		);

		$args = array(
			'before_widget' => '<div class="before_widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);

		$this->assertRegExp(
			'/<form role="search" method="get" class="search-form" action="https:\/\/www.secureserver.net\/products\/domain-transfer\/\?plid=12345">/',
			$widget->widget( $args, $instance )
		);

	}

	/**
	 * @testdox Given a new instance the instance should update
	 */
	function test_widget_update() {

		$widget = new Widgets\Domain_Transfer();

		$old_instance = array(
			'title'            => '',
			'text_placeholder' => '',
			'text_search'      => '',
		);

		$new_instance = array(
			'title'            => 'title 1',
			'text_placeholder' => 'placeholder',
			'text_search'      => 'text_search',
			'new_tab'          => false,
		);

		$instance = $widget->update( $new_instance, $old_instance );

		foreach ( $instance as $key => $value ) {
			$this->assertEquals( $instance[ $key ], $new_instance[ $key ] );
		}

	}

	/**
	 * @testdox Given an instance the form should render
	 */
	function test_widget_form() {

		$widget = new Widgets\Domain_Transfer();

		$instance = array(
			'title'            => 'aaa',
			'text_placeholder' => 'bbb',
			'text_search'      => 'ccc',
		);

		$widget->form( $instance );

		foreach ( $instance as $key => $value ) {
			$this->expectOutputRegex( '/<input type="text" id="widget-rstore_transfer--title" name="widget-rstore_transfer\[\]\[title\]" value="aaa" class="widefat">/' );
		}

	}

	/**
	 * @testdox Given an a cart widget classes filter it should render
	 */
	function test_widget_filter() {

		add_filter(
			'rstore_domain_transfer_widget_classes',
			function( $title ) {
				return array( 'transfer' );
			}
		);

		$widget = new Widgets\Domain_Transfer();

		rstore_update_option( 'pl_id', 12345 );

		$post = Tests\Helper::create_product();

		$instance = array(
			'post_id'    => $post->ID,
			'image_size' => 'full',
			'show_title' => true,
		);

		$args = array(
			'before_widget' => '<div class="before_widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);

		$this->assertRegExp(
			'/<div class="before_widget transfer">/',
			$widget->widget( $args, $instance )
		);

	}


}
