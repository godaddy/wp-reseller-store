<?php

namespace Reseller_Store;

final class TestWidget extends TestCase {

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

    global $wp_widget_factory;

    // Check cart widget presence
    $this->assertTrue(
      class_exists(  __NAMESPACE__ . '\Widgets\Cart' ),
      'Class \Widgets\Cart is not found'
    );

    new Widgets;


  //   // $this->assertTrue( isset( $wp_widget_factory->widgets[__NAMESPACE__ . 'Widgets\Cart'] ) );

    $widgets = array_keys( $wp_widget_factory->widgets );
    print var_dump($widgets);
  //   print 'adsfadfadsf';

  //   // // Check social widget class presence
  //   // $this->assertTrue(
  //   //   class_exists( 'WPCW\Social' ),
  //   //   'Class WPCW\Social is not found'
  //   // );

  //   // $this->assertTrue( isset( $wp_widget_factory->widgets['WPCW\Social'] ) );

  }

}
