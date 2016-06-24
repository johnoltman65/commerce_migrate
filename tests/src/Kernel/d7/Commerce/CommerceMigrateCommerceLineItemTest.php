<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;

use Drupal\commerce_order\Entity\LineItem;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate
 */
class CommerceMigrateCommerceLineItemTest extends CommerceMigrateCommerce1TestBase {

  static $modules = [
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
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('view');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_line_item');
    // We need to install config so we have a default line item bundle.
    // @todo provide way to port line item types properly.
    $this->installConfig(['commerce_order']);
    $this->executeMigrations([
//      'd7_field',
//      'd7_field_instance',
      'd7_product_type',
      'd7_product',
      'd7_line_item',
    ]);
  }

  /**
   * Test line item migration from Drupal 7 to 8.
   */
  public function testLineItem() {
    $line_item = LineItem::load(1);
    $this->assertNotNull($line_item);
    $this->assertEquals('TSH3-LTB-MD', $line_item->label());
    $this->assertEquals($line_item->getCreatedTime(), 1458216500);
    $this->assertEquals($line_item->getChangedTime(), 1458216500);
    $this->assertEquals(1, $line_item->getQuantity());
    $this->assertEquals('38.00', $line_item->getUnitPrice()->getDecimalAmount());
    $this->assertEquals('38.00', $line_item->getTotalPrice()->getDecimalAmount());

    $this->assertNull($line_item->getPurchasedEntity());
  }

}
