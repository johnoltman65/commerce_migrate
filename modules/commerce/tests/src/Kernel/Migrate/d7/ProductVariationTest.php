<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate_commerce
 */
class ProductVariationTest extends Commerce1TestBase {

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
    // @todo Execute the d7_field and d7_field_instance migrations?
    $this->executeMigrations([
      'd7_product_type',
      'd7_product',
    ]);
  }

  /**
   * Test product variation migration from Drupal 7 Commerce to Drupal 8.
   */
  public function testProductVariation() {
    $this->assertProductVariationEntity(1, '0', 'TOT1-GRN-OS', '16.000000', 'USD', '1', 'Tote Bag 1', 'default');
    $this->assertProductVariationEntity(11, '0', 'HAT1-GRY-OS', '16.000000', 'USD', '11', 'Hat 1', 'default');
    $this->assertProductVariationEntity(19, '0', 'SHO2-PRL-04', '40.000000', 'USD', '19', 'Shoe 2', 'default');
    $this->assertProductVariationEntity(20, '0', 'SHO2-PRL-05', '40.000000', 'USD', '20', 'Shoe 2', 'default');
    $this->assertProductVariationEntity(28, '0', 'USB-BLU-08', '11.990000', 'USD', '28', 'Storage 1', 'default');
    $this->assertProductVariationEntity(29, '0', 'USB-BLU-16', '17.990000', 'USD', '29', 'Storage 1', 'default');
    $this->assertProductVariationEntity(30, '0', 'USB-BLU-32', '29.990000', 'USD', '30', 'Storage 1', 'default');

    $product = ProductVariation::load(1);
    $this->assertEquals($product->getCreatedTime(), 1493287314);
    $this->assertEquals($product->getChangedTime(), 1493287314);
  }

}
