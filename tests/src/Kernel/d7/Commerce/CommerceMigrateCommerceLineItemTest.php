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
  ];

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('commerce_line_item');
    $this->executeMigrations([
//      'd7_field',
//      'd7_field_instance',
      'd7_line_item',
    ]);
  }

  /**
   * Test profile migration from Drupal 7 to 8.
   */
  public function testProfile() {
    $line_item = LineItem::load(1);
    $this->assertNotNull($line_item);
    $this->assertEquals($line_item->label(), 'TSH3-LTB-MD');
    $this->assertEquals($line_item->getCreatedTime(), 1458216500);
    $this->assertEquals($line_item->getChangedTime(), 1458216500);
  }
}
