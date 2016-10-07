<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;

use Drupal\commerce_product\Entity\ProductVariation;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate
 */
class ProductVariationTest extends Commerce1TestBase {

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
    $this->assertEquals(16, $product->getPrice()->getNumber());
  }

}
