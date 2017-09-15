<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests store migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class MigrateStoreTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->migrateStore();
  }

  /**
   * Test store migration from Drupal 7 to 8.
   */
  public function testStore() {
    $this->assertStoreEntity(1, 'Commerce Kickstart', 'CommerceKickstart@example.com', 'USD', 'online', '1');
  }

}
