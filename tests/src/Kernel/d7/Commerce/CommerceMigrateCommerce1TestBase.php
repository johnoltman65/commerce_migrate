<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;

use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

abstract class CommerceMigrateCommerce1TestBase extends MigrateDrupal7TestBase {

  static $modules = [
    'action',
//    'comment',
//    'node',
//    'taxonomy_term',
    'profile',
    'address',
    'state_machine',
    'commerce',
    'commerce_price',
    'commerce_store',
    'commerce_order',
    'commerce_migrate',
  ];

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->loadFixture(__DIR__ . '/../../../../fixtures/ck2-fixture.php');
    $this->installConfig(static::$modules);
  }

}
