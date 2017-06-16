<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\commerce_product\Entity\ProductVariation;

/**
 * Tests Product migration.
 *
 * @group commerce_migrate
 */
class ProductVariationTest extends Ubercart6TestBase {

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
    $variation = ProductVariation::load(1);
    $this->assertNotNull($variation);
    $this->assertEquals('towel-bath-001', $variation->getSku());
    $this->assertEquals('20.00000', $variation->getPrice()->getNumber());

    $product = $variation->getProduct();
    $this->assertNotNull($product);
    $this->assertEquals(1, $product->id());
    $this->assertEquals('Bath Towel', $product->getTitle());

    $this->assertEquals('NZD', $variation->getPrice()->getCurrencyCode());
  }

}
