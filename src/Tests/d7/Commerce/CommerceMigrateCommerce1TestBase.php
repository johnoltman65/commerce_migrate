<?php

namespace Drupal\commerce_migrate\Tests\d7\Commerce;

use Drupal\migrate_drupal\Tests\d7\MigrateDrupal7TestBase;

abstract class CommerceMigrateCommerce1TestBase extends MigrateDrupal7TestBase {

  static $modules = [
    'commerce',
  ];

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->loadFixture(__DIR__ . '/../../../../tests/fixtures/ck2-fixture.php');
    $this->installMigrations('Drupal Commerce 1');
  }

}
