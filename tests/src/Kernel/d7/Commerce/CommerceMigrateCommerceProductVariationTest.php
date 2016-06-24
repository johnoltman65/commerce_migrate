<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;

use Drupal\commerce_product\Entity\ProductVariation;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate
 */
class CommerceMigrateCommerceProductVariationTest extends CommerceMigrateCommerce1TestBase {

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
   * {@inheritdoc}
   */
  protected function setUp() {
    error_reporting(E_ALL);
    parent::setUp();
    $this->installEntitySchema('view');
    $this->installEntitySchema('commerce_product_variation');
    $this->executeMigrations([
//      'd7_field',
//      'd7_field_instance',
      'd7_product_type',
      'd7_product',
    ]);
  }

  /**
   * Test profile migration from Drupal 7 to 8.
   */
  public function testProductVariation() {
    $product = ProductVariation::load(1);
    $this->assertNotNull($product);
    $this->assertEquals($product->getSku(), 'TOT1-GRN-OS');
    $this->assertEquals($product->label(), 'Tote Bag 1');
    $this->assertEquals($product->getCreatedTime(), 1458216500);
    $this->assertEquals($product->getChangedTime(), 1458216500);
    $this->assertEquals(16, $product->getPrice()->getDecimalAmount());
  }

}
