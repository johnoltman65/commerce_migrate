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
      'd7_product_type',
      'd7_product',
      'd7_product_display_type',
    ]);

    $this->executeMigrations([
      'd7_product_display',
    ]);
  }

  /**
   * Test profile migration from Drupal 7 to 8.
   */
  public function testProduct() {
    $this->assertProductEntity(15, '1', 'Go green with Drupal Commerce Reusable Tote Bag', TRUE, ['1'], ['1']);

    $product = Product::load(15);
    $variation_id = $product->variations->target_id;
    $this->assertProductVariationEntity($variation_id, '0', 'TOT1-GRN-OS', '16.000000', 'USD', '1', 'Tote Bag 1', 'default');
  }

}
