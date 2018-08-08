<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc7;

use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

/**
 * Test base for Ubercart 7 tests.
 */
abstract class Ubercart7TestBase extends MigrateDrupal7TestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'commerce',
    'commerce_migrate_ubercart',
  ];

  /**
   * Executes store migrations.
   */
  protected function migrateStore() {
    $this->enableModules([
      'address',
      'commerce_price',
      'commerce_store',
    ]);
    $this->installEntitySchema('commerce_store');

    $this->executeMigrations([
      'd7_filter_format',
      'd7_user_role',
      'd7_user',
      'uc_currency',
      'uc7_store',
    ]);
  }

  /**
   * Gets the path to the fixture file.
   */
  protected function getFixtureFilePath() {
    return __DIR__ . '/../../../../fixtures/uc7.php';
  }

  /**
   * Creates a default store.
   */
  protected function createDefaultStore() {
    $currency_importer = \Drupal::service('commerce_price.currency_importer');
    /** @var \Drupal\commerce_store\StoreStorage $store_storage */
    $store_storage = \Drupal::service('entity_type.manager')->getStorage('commerce_store');

    $currency_importer->import('USD');
    $store_values = [
      'type' => 'default',
      'uid' => 1,
      'name' => 'Demo store',
      'mail' => 'admin@example.com',
      'address' => [
        'country_code' => 'US',
      ],
      'default_currency' => 'USD',
    ];
    /** @var \Drupal\commerce_store\Entity\Store $store */
    $store = $store_storage->create($store_values);
    $store->save();
    $store_storage->markAsDefault($store);
  }

}
