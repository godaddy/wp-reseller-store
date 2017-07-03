<?php

namespace Reseller_Store;

final class TestWidget extends TestCase {

  function test_basics() {
    // Check cart widget presence
    $this->assertTrue(
      class_exists(  __NAMESPACE__ . '\Widgets\Cart' ),
      'Class \Widgets\Cart is not found'
    );

    // Check Domain_Search widget presence
    $this->assertTrue(
      class_exists(  __NAMESPACE__ . '\Widgets\Domain_Search' ),
      'Class \Widgets\Domain_Search is not found'
    );

    // Check Product widget presence
    $this->assertTrue(
      class_exists(  __NAMESPACE__ . '\Widgets\Product' ),
      'Class \Widgets\Product is not found'
    );
  }

  /**
   * Test that all required actions and filters are added as expected
   */
  function test_init() {

    new Widgets;

    $this->do_action_validation( 'widgets_init', [ __NAMESPACE__ . '\Widgets', 'register_widgets' ] );
  }


  /**
   * Test for register_widget function
   */
  function test_register_widget() {

    Widgets::register_widgets();

    global $wp_widget_factory;



  }


  // function test_cart_widget() {

  //   $this->plugin = rstore();

  //   echo var_dump($this->plugin);

  //   $this->expectOutputRegex( '/class="wpcw-widget wpcw-widget-social"/' );
  //   $this->expectOutputRegex( '/class="customizer_update"/' );
  //   $this->expectOutputRegex( '/class="default-fields"/' );

  //   $this->plugin->form( [] );

  // }

}
