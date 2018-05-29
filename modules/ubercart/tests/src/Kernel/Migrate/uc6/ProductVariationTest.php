<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc6;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;
use Drupal\commerce_product\Entity\ProductVariation;

/**
 * Tests Product variation migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc6
 */
class ProductVariationTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'commerce_product',
    'filter',
    'menu_ui',
    'node',
    'path',
    'migrate_plus',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('view');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_product');
    $this->installConfig(static::$modules);
    $this->migrateStore();
    $this->executeMigrations([
      'd6_filter_format',
      'd6_user_role',
      'd6_user',
      'd6_node_type',
      'uc6_product_type',
      'd6_field',
      'd6_field_instance',
      'uc6_attribute_field',
      'uc6_product_attribute',
      'uc6_attribute_field_instance',
      'uc6_attribute_instance_widget_settings',
      'uc6_product_variation',
      'd6_view_modes',
      'd6_field_formatter_settings',
      'd6_node',
    ]);
  }

  /**
   * Test product variation migration.
   */
  public function testProductVariation() {
    $this->assertProductVariationEntity(1, '1', 'towel-bath-001', '20.000000', 'NZD', '1', 'Bath Towel', 'default', '1492867780', NULL);
    $this->assertProductVariationEntity(2, '1', 'towel-beach-001', '15.000000', 'NZD', '2', 'Beach Towel', 'default', '1492989418', NULL);
    $this->assertProductVariationEntity(3, '1', 'Fairy-Cake-001', '1500.000000', 'NZD', '3', 'Fairy cake', 'default', '1492989703', NULL);
    $this->assertProductVariationEntity(4, '1', 'ship-001', '6000000000.000000', 'NZD', '4', 'Golgafrincham B-Ark', 'default', '1500868190', NULL);
    $this->assertProductVariationEntity(5, '1', 'ship-002', '123000000.000000', 'NZD', '5', 'Heart of Gold', 'default', '1500868361', NULL);
    $variation = ProductVariation::load(6);
    $this->assertNull($variation);
    $variation = ProductVariation::load(7);
    $this->assertNull($variation);
  }

}
