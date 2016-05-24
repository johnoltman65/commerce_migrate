<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;

use Drupal\commerce_product\Entity\Product;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate
 */
class CommerceMigrateCommerceProductTest extends CommerceMigrateCommerce1TestBase {

  static $modules = [
    'action',
    'system',
    'entity',
    'views',
    'text',
    'path',
    'inline_entity_form',
    'commerce_product',
  ];

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('view');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_product');
    $this->createDefaultStore();
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
    $product = Product::load(15);
    $this->assertNotNull($product);
    $this->assertEquals('Go green with Drupal Commerce Reusable Tote Bag', $product->label());

    /** @var \Drupal\commerce_product\Entity\ProductVariationInterface $variation */
    $variation = $product->variations->first()->entity;
    $this->assertNotEmpty($variation);
    $this->assertEquals($variation->getSku(), 'TOT1-GRN-OS');
    $this->assertEquals($variation->label(), 'Tote Bag 1');


    $product = Product::load(26);
    $this->assertEquals('Commerce Guys USB Key', $product->label());
    $this->assertEquals(3, $product->variations->count());
  }

}
