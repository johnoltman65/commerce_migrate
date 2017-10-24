<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
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
      'd7_commerce_product_variation_type',
      'd7_commerce_product_variation',
    ]);
  }

  /**
   * Test product variation migration from Drupal 7 Commerce to Drupal 8.
   */
  public function testProductVariation() {
    $variation = [
      'id' => 1,
      'uid' => '0',
      'sku' => 'TOT1-GRN-OS',
      'price' => '16.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'variation_title' => 'Tote Bag 1',
      'variation_bundle' => 'default',
    ];
    $this->assertProductVariationEntity($variation['id'], $variation['uid'], $variation['sku'], $variation['price'], $variation['currency'], $variation['product_id'], $variation['variation_title'], $variation['variation_bundle']);
    $variation = [
      'id' => 11,
      'uid' => '0',
      'sku' => 'HAT1-GRY-OS',
      'price' => '16.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'variation_title' => 'Hat 1',
      'variation_bundle' => 'default',
    ];
    $this->assertProductVariationEntity($variation['id'], $variation['uid'], $variation['sku'], $variation['price'], $variation['currency'], $variation['product_id'], $variation['variation_title'], $variation['variation_bundle']);
    $variation = [
      'id' => 12,
      'uid' => '0',
      'sku' => 'HAT2-BLK-OS',
      'price' => '12.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'variation_title' => 'Hat 2',
      'variation_bundle' => 'default',
    ];
    $this->assertProductVariationEntity($variation['id'], $variation['uid'], $variation['sku'], $variation['price'], $variation['currency'], $variation['product_id'], $variation['variation_title'], $variation['variation_bundle']);
    $variation = [
      'id' => 12,
      'uid' => '0',
      'sku' => 'HAT2-BLK-OS',
      'price' => '12.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'variation_title' => 'Hat 2',
      'variation_bundle' => 'default',
    ];
    $this->assertProductVariationEntity($variation['id'], $variation['uid'], $variation['sku'], $variation['price'], $variation['currency'], $variation['product_id'], $variation['variation_title'], $variation['variation_bundle']);
    $variation = [
      'id' => 19,
      'uid' => '0',
      'sku' => 'SHO2-PRL-04',
      'price' => '40.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'variation_title' => 'Shoe 2',
      'variation_bundle' => 'default',
    ];
    $this->assertProductVariationEntity($variation['id'], $variation['uid'], $variation['sku'], $variation['price'], $variation['currency'], $variation['product_id'], $variation['variation_title'], $variation['variation_bundle']);
    $variation = [
      'id' => 20,
      'uid' => '0',
      'sku' => 'SHO2-PRL-05',
      'price' => '40.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'variation_title' => 'Shoe 2',
      'variation_bundle' => 'default',
    ];
    $this->assertProductVariationEntity($variation['id'], $variation['uid'], $variation['sku'], $variation['price'], $variation['currency'], $variation['product_id'], $variation['variation_title'], $variation['variation_bundle']);
    $variation = [
      'id' => 28,
      'uid' => '0',
      'sku' => 'USB-BLU-08',
      'price' => '11.990000',
      'currency' => 'USD',
      'product_id' => NULL,
      'variation_title' => 'Storage 1',
      'variation_bundle' => 'default',
    ];
    $this->assertProductVariationEntity($variation['id'], $variation['uid'], $variation['sku'], $variation['price'], $variation['currency'], $variation['product_id'], $variation['variation_title'], $variation['variation_bundle']);
    $variation = [
      'id' => 29,
      'uid' => '0',
      'sku' => 'USB-BLU-16',
      'price' => '17.990000',
      'currency' => 'USD',
      'product_id' => NULL,
      'variation_title' => 'Storage 1',
      'variation_bundle' => 'default',
    ];
    $this->assertProductVariationEntity($variation['id'], $variation['uid'], $variation['sku'], $variation['price'], $variation['currency'], $variation['product_id'], $variation['variation_title'], $variation['variation_bundle']);
    $variation = [
      'id' => 30,
      'uid' => '0',
      'sku' => 'USB-BLU-32',
      'price' => '29.990000',
      'currency' => 'USD',
      'product_id' => NULL,
      'variation_title' => 'Storage 1',
      'variation_bundle' => 'default',
    ];
    $this->assertProductVariationEntity($variation['id'], $variation['uid'], $variation['sku'], $variation['price'], $variation['currency'], $variation['product_id'], $variation['variation_title'], $variation['variation_bundle']);
  }

  /**
   * Test timestamps.
   */
  public function testTimestamps() {
    $product = ProductVariation::load(1);
    $this->assertEquals($product->getCreatedTime(), 1493287314);
    $this->assertEquals($product->getChangedTime(), 1493287350);
  }

}
