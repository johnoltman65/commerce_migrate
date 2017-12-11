<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests product attribute value migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class ProductAttributeValueTest extends Ubercart6TestBase {

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
    $this->installEntitySchema('commerce_product_attribute_value');
    $this->executeMigrations([
      'd6_ubercart_attribute_field',
      'd6_ubercart_product_attribute',
      'd6_ubercart_product_attribute_value',
    ]);
  }

  /**
   * Test product attribute value migration.
   */
  public function testProductAttributeValue() {
    $this->assertProductAttributeValueEntity('1', 'design', 'Heart of Gold', 'Heart of Gold', '0');
    $this->assertProductAttributeValueEntity('2', 'design', 'Trillian', 'Trillian', '0');
    $this->assertProductAttributeValueEntity('3', 'design', 'Pan Galactic Gargle Blaster', 'Pan Galactic Gargle Blaster', '0');
    $this->assertProductAttributeValueEntity('4', 'color', 'White', 'White', '500');
    $this->assertProductAttributeValueEntity('5', 'color', 'Gold', 'Gold', '500');
    $this->assertProductAttributeValueEntity('6', 'model_size', 'Keychain', 'Keychain', '20');
    $this->assertProductAttributeValueEntity('7', 'model_size', 'Desk', 'Desk', '400');
  }

}
