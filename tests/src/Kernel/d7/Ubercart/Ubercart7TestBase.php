<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Ubercart;

use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

/**
 * Test base for Ubercart D7 tests.
 */
abstract class Ubercart7TestBase extends MigrateDrupal7TestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'action',
    'profile',
    'address',
    'entity',
    'inline_entity_form',
    'state_machine',
    'commerce',
    'commerce_price',
    'commerce_store',
    'commerce_order',
    'commerce_migrate',
    'views',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->loadFixture(__DIR__ . '/../../../../fixtures/uc7-fixture.php');
    $this->installEntitySchema('commerce_store');
    $this->installConfig(static::$modules);
  }

  /**
   * Helper method to ensure default store is present.
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
    $store = $store_storage->create($store_values);
    $store->save();
    $store_storage->markAsDefault($store);
  }

}
