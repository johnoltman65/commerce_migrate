<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\commerce_order\Entity\OrderItem;

/**
 * Tests order item migration.
 *
 * @group commerce_migrate_commerce
 */
class OrderItemTest extends Commerce1TestBase {

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
    $this->installEntitySchema('commerce_order_item');
    // We need to install config so we have a default order item type.
    // @todo provide way to migrate line item types properly.
    $this->installConfig(['commerce_order']);
    // @todo Execute the d7_field and d7_field_instance migrations?
    $this->executeMigrations([
      'd7_product_type',
      'd7_product',
      'd7_order_item_type',
      'd7_line_item',
    ]);
  }

  /**
   * Test line item migration from Drupal 7 to 8.
   */
  public function testOrderItem() {
    $order_item = OrderItem::load(1);
    $this->assertNotNull($order_item);
    $this->assertEquals('HAT2-BLK-OS', $order_item->label());
    $this->assertEquals($order_item->getCreatedTime(), 1493287432);
    $this->assertEquals($order_item->getChangedTime(), 1493287432);
    $this->assertEquals(1, $order_item->getQuantity());
    $this->assertEquals('12.000000', $order_item->getUnitPrice()->getNumber());
    $this->assertEquals('12.000000', $order_item->getTotalPrice()->getNumber());

    $this->assertNotNull($order_item->getPurchasedEntity());
  }

}
