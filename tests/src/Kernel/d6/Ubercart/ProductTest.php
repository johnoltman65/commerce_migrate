<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\commerce_product\Entity\Product;

/**
 * Tests Product migration.
 *
 * @group commerce_migrate
 */
class ProductTest extends Ubercart6TestBase {

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
      'ubercart_currency',
      'd6_ubercart_product_variation',
      'd6_ubercart_product',
    ]);
  }

  /**
   * Test product migration from Drupal 6 to 8.
   */
  public function testProduct() {
    /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
    $product = Product::load(1);
    $this->assertNotNull($product);
    $this->assertEquals('Bath Towel', $product->getTitle());
    $this->assertEquals(TRUE, $product->isPublished());
    $this->assertNotEmpty($product->getStoreIds());

    /** @var \Drupal\commerce_product\Entity\ProductVariationInterface $variation */
    $variation = $product->variations->first()->entity;
    $this->assertNotEmpty($variation);
    $this->assertEquals('towel-bath-001', $variation->getSku());

    $product = Product::load(3);
    $this->assertNotNull($product);
    $this->assertEquals('Fairy cake', $product->getTitle());
    $this->assertEquals(1492989703, $product->getChangedTime());
  }

}
