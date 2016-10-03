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
    $this->createDefaultStore();
    $this->executeMigrations([
      'd6_ubercart_product_variation',
      'd6_ubercart_product',
    ]);
  }

  /**
   * Test product migration from Drupal 6 to 8.
   */
  public function testProduct() {
    $product = Product::load(1);
    $this->assertNotNull($product);
    $this->assertEquals('Product 27', $product->getTitle());
    $this->assertEquals(FALSE, $product->isPublished());

    /** @var \Drupal\commerce_product\Entity\ProductVariationInterface $variation */
    $variation = $product->variations->first()->entity;
    $this->assertNotEmpty($variation);
    $this->assertEquals('MODEL-27', $variation->getSku());

    $product = Product::load(3);
    $this->assertNotNull($product);
    $this->assertEquals('Product 29', $product->getTitle());
    $this->assertEquals(1346271526, $product->getChangedTime());
  }

}
