<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests product variation type migration.
 *
 * @group commerce_migrate_commerce
 */
class ProductVariationTypeTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

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
    parent::setUp();
    $this->installEntitySchema('view');
    $this->installEntitySchema('commerce_product_variation');
    $this->executeMigrations([
      'd7_commerce_product_variation_type',
      'd7_commerce_product_variation',
    ]);
  }

  /**
   * Test product variation type migration from Drupal 7 to 8.
   *
   * Product variation types in Drupal 8 are product types in Drupal 7.
   */
  public function testProductVariationType() {
    $this->assertProductVariationTypeEntity('bags_cases', 'Bags & Cases', 'default', FALSE);
    $this->assertProductVariationTypeEntity('hats', 'Hats', 'default', FALSE);
  }

}
