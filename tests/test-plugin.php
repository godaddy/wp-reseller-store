<?php

namespace Reseller_Store;

final class TestPlugin extends TestCase {

  function setUp() {

    parent::setUp();
  }

  /**
   * Test that Plugin exists.
   */
  public function test_basics() {
    // $this->assertTrue( class_exists( __NAMESPACE__ . 'Plugin' ) );
    $this->assertTrue( function_exists( 'rstore' ) );
  }

}
