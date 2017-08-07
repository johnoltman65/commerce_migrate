<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\commerce_store\Entity\Store;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests store migration.
 *
 * @group commerce_migrate
 */
class MigrateDefaultStoreTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->migrateStore();
    $this->executeMigration('d7_commerce_default_store');
  }

  /**
   * Test default store migration from Drupal 7 to 8.
   */
  public function testMigrateDefaultStore() {
    $defaultStore = $this->container->get('commerce_store.default_store_resolver')->resolve();
    $this->assertInstanceOf(Store::class, $defaultStore);
  }

}
