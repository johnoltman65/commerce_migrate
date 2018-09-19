<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc7;

use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

/**
 * Test base for Ubercart 7 tests.
 */
abstract class Ubercart7TestBase extends MigrateDrupal7TestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    // Commerce requirements.
    'address',
    'commerce',
    'entity',
    'entity_reference_revisions',
    'inline_entity_form',
    'views',
    // Commerce migrate requirements.
    'commerce_migrate',
    'commerce_migrate_ubercart',
  ];

  /**
   * Gets the path to the fixture file.
   */
  protected function getFixtureFilePath() {
    return __DIR__ . '/../../../../fixtures/uc7.php';
  }

  /**
   * Executes attributes migrations.
   *
   * Required modules:
   * - commerce_price.
   * - commerce_product.
   * - commerce_store.
   * - path.
   */
  protected function migrateAttributes() {
    $this->installEntitySchema('commerce_product_variation');
    $this->installConfig(['commerce_product']);
    $this->executeMigrations([
      'uc_attribute_field',
      'uc_product_attribute',
      'uc_attribute_field_instance',
      'uc_attribute_instance_widget_settings',
    ]);
  }

  /*
   * Migrate content types migrations.
   *
   * Required modules:
   * - commerce_product.
   * - node.
   */
  protected function migrateContentTypes() {
    $this->installConfig(['commerce_product', 'node']);
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('node');
    $this->executeMigrations([
      'd7_node_type',
      'uc7_product_type',
    ]);
  }
  /**
   * Executes order item migration.
   *
   * Required modules:
   * - commerce_order.
   * - commerce_price.
   * - commerce_product.
   * - commerce_store.
   * - migrate_plus.
   * - path.
   * - profile.
   * - state_machine.
   */
  protected function migrateOrderItems() {
    $this->installEntitySchema('view');
    $this->installEntitySchema('profile');
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installEntitySchema('node');
    $this->installConfig(['commerce_order', 'commerce_product']);
    $this->migrateStore();
    $this->migrateContentTypes();
    $this->migrateAttributes();
    $this->executeMigrations([
      'uc7_product_variation',
      'd7_node',
      'uc7_profile_billing',
      'uc7_order_product',
    ]);
  }

  /**
   * Executes order migration.
   *
   * Required modules:
   * - commerce_order.
   * - commerce_price.
   * - commerce_product.
   * - commerce_store.
   * - migrate_plus.
   * - path.
   * - profile.
   * - state_machine.
   */
  protected function migrateOrders() {
    $this->migrateOrderItems();
    $this->executeMigration('uc7_order');
  }

  /**
   * Executes all user migrations.
   */
  protected function migrateUsers() {
    $this->executeMigrations(['d7_user_role', 'd7_user']);
  }

  /**
   * Executes store migration.
   *
   * Required modules:
   * - commerce_price.
   * - commerce_store.
   */
  protected function migrateStore() {
    $this->installEntitySchema('commerce_store');
    $this->migrateUsers();
    $this->executeMigrations([
      'uc_currency',
      'uc7_store',
    ]);
  }

}
