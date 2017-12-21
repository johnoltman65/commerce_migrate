<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests product attribute migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class ProductAttributeTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['commerce_product'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->executeMigrations([
      'd6_ubercart_attribute_field',
      'd6_ubercart_product_attribute',
    ]);
  }

  /**
   * Test attribute migration from Drupal 6 to 8.
   */
  public function testMigrateProductAttributeTest() {
    $this->assertProductAttributeEntity('design', 'Cool Designs for your towel', 'radios');
    $this->assertProductAttributeEntity('color', 'Color', 'checkbox');
    // Tests that the attribute name longer than 32 characters is truncated.
    $this->assertProductAttributeEntity('model_size_attribute', 'Model size', 'select');
    $this->assertProductAttributeEntity('name', 'Name', 'text');
  }

}
