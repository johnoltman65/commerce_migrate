<?php

namespace Drupal\commerce_migrate\Tests\d7\Commerce;

use Drupal\migrate_drupal\Tests\d7\MigrateDrupal7TestBase;

abstract class CommerceMigrateCommerce1TestBase extends MigrateDrupal7TestBase {

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->loadFixture('../../../../tests/fixtures/ck2-fixture.php');
  }

}
