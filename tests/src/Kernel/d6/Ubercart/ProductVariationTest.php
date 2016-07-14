<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;

/**
 * Tests Product migration.
 *
 * @group commerce_migrate
 */
class ProductVariationTest extends Ubercart6TestBase {

  public static $modules = [
    'text',
    'action',
    'system',
    'entity',
    'views',
    'path',
    'inline_entity_form',
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
   * Test product variation migration from Drupal 6 to 8.
   */
  public function testProductVariation() {
    $variation = ProductVariation::load(1);
    $this->assertNotNull($variation);
    $this->assertEquals('MODEL-27', $variation->getSku());
    $this->assertEquals('60.000000', $variation->getPrice()->getDecimalAmount());

    $product = $variation->getProduct();
    $this->assertNotNull($product);
    $this->assertEquals(1, $product->id());
    $this->assertEquals('Product 27', $product->getTitle());

    $variation = ProductVariation::load(5);
    $this->assertNotNull($variation);
    $this->assertEquals('USD', $variation->getPrice()->getCurrencyCode());
  }

}
