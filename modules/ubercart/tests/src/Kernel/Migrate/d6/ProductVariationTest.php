<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests Product migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class ProductVariationTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'path',
    'commerce_product',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('view');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_product');
    $this->installConfig(['commerce_product']);
    $this->migrateStore();
    $this->executeMigrations([
      'd6_ubercart_product_variation',
      'd6_ubercart_product',
    ]);
  }

  /**
   * Test product variation migration from Drupal 6 to 8.
   */
  public function testProductVariation() {
    $this->assertProductVariationEntity(1, '1', 'towel-bath-001', '20.000000', 'NZD', '1', 'Bath Towel', 'default');
    $this->assertProductEntity(1, '1', 'Bath Towel', TRUE, ['1'], ['1']);

    $this->assertProductVariationEntity(2, '1', 'towel-beach-001', '15.000000', 'NZD', '2', 'Beach Towel', 'default');
    $this->assertProductEntity(2, '1', 'Beach Towel', TRUE, ['1'], ['2']);

    $this->assertProductVariationEntity(3, '1', 'Fairy-Cake-001', '1500.000000', 'NZD', '3', 'Fairy cake', 'default');
    $this->assertProductEntity(3, '1', 'Fairy cake', TRUE, ['1'], ['3']);
  }

}
