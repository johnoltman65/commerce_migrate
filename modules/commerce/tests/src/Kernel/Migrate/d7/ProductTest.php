<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

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
   * Test product migration from Drupal 7 to 8.
   */
  public function testProduct() {
    $product = [
      'product_id' => '15',
      'uid' => '1',
      'title' => 'Go green with Drupal Commerce Reusable Tote Bag',
      'published' => TRUE,
      'store_ids' => ['1'],
      'variations' => [
        [
          'variation_id' => '1',
          'uid' => '0',
          'sku' => 'TOT1-GRN-OS',
          'price' => '16.000000',
          'currency' => 'USD',
          'title' => 'Tote Bag 1',
          'order_item_type' => 'default',
        ]
      ],
    ];
    $this->productTest($product);

    // Tests a product with multiple variations.
    $product = [
      'product_id' => '26',
      'uid' => '1',
      'title' => 'Commerce Guys USB Key',
      'published' => TRUE,
      'store_ids' => ['1'],
      'variations' => [
        [
          'variation_id' => '28',
          'uid' => '0',
          'sku' => 'USB-BLU-08',
          'price' => '11.990000',
          'currency' => 'USD',
          'title' => 'Storage 1',
          'order_item_type' => 'default',
        ],
        [
          'variation_id' => '29',
          'uid' => '0',
          'sku' => 'USB-BLU-16',
          'price' => '17.990000',
          'currency' => 'USD',
          'title' => 'Storage 1',
          'order_item_type' => 'default',
        ],
        [
          'variation_id' => '30',
          'uid' => '0',
          'sku' => 'USB-BLU-32',
          'price' => '29.990000',
          'currency' => 'USD',
          'title' => 'Storage 1',
          'order_item_type' => 'default',
        ],
      ],
    ];
    $this->productTest($product);
  }

}
