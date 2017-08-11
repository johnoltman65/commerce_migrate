<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\commerce_product\Entity\Product;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate_commerce
 */
class ProductTest extends Commerce1TestBase {

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
    $this->migrateStore();
    $this->executeMigrations([
      'd7_user_role',
      'd7_user',
      'd7_commerce_product_variation_type',
      'd7_commerce_product_variation',
      'd7_commerce_product_type',
    ]);

    $this->executeMigrations([
      'd7_commerce_product',
    ]);
  }

  /**
   * Test profile migration from Drupal 7 to 8.
   */
  public function testProduct() {
    $this->assertProductEntity(15, '1', 'Go green with Drupal Commerce Reusable Tote Bag', TRUE, ['1'], ['1']);

    $product = Product::load(15);
    $variation_id = $product->variations->target_id;
    $this->assertProductVariationEntity($variation_id, '0', 'TOT1-GRN-OS', '16.000000', 'USD', '15', 'Tote Bag 1', 'default');

    // Tests a product with multiple variations.
    $this->assertProductEntity(26, '1', 'Commerce Guys USB Key', TRUE, ['1'], ['28', '29', '30']);
    $this->assertProductVariationEntity(28, '0', 'USB-BLU-08', '11.990000', 'USD', '26', 'Storage 1', 'default');
    $this->assertProductVariationEntity(29, '0', 'USB-BLU-16', '17.990000', 'USD', '26', 'Storage 1', 'default');
    $this->assertProductVariationEntity(30, '0', 'USB-BLU-32', '29.990000', 'USD', '26', 'Storage 1', 'default');
  }

}
