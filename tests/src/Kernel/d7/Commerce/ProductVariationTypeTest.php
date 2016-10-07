<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;

use Drupal\commerce_product\Entity\ProductVariationType;

/**
 * Tests product variation type migration.
 *
 * @group commerce_migrate
 */
class ProductVariationTypeTest extends Commerce1TestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
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
      'd7_product_type',
      'd7_product',
    ]);
  }

  /**
   * Test product variation type migration from Drupal 7 to 8.
   *
   * Product variation types in Drupal 8 are product types in Drupal 7.
   */
  public function testProductVariationType() {
    $variation_type = ProductVariationType::load('bags_cases');
    $this->assertNotNull($variation_type);
    $this->assertEquals($variation_type->label(), 'Bags & Cases');
    $this->assertFalse($variation_type->shouldGenerateTitle());
    $this->assertEquals($variation_type->getOrderItemTypeId(), 'default');
  }

}
