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
   * Test product attribute migration.
   */
  public function testProductAttribute() {
    $this->assertProductAttributeEntity('commerce_product_attribute.design', 'Cool Designs for your towel', 'radios');
    $this->assertProductAttributeEntity('commerce_product_attribute.color', 'Color', 'checkbox');
    $this->assertProductAttributeEntity('commerce_product_attribute.model_size', 'Model size', 'select');
    $this->assertProductAttributeEntity('commerce_product_attribute.name', 'Name', 'text');
  }

}
