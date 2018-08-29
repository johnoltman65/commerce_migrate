<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc6;

use Drupal\commerce_order\Entity\OrderItem;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests order item migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc6
 */
class OrderItemTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'commerce_order',
    'commerce_price',
    'commerce_product',
    'commerce_store',
    'migrate_plus',
    'path',
    'profile',
    'state_machine',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->migrateOrderItems();
  }

  /**
   * Test order item migration.
   */
  public function testOrderItem() {
    $this->assertOrderItem(2, NULL, 3, '1.00', 'Fairy cake', '1500.000000', 'NZD', '1500.000000', 'NZD');
    $this->assertOrderItem(3, NULL, 1, '1.00', 'Bath Towel', '20.000000', 'NZD', '20.000000', 'NZD');
    $this->assertOrderItem(4, NULL, 2, '1.00', 'Beach Towel', '15.000000', 'NZD', '15.000000', 'NZD');

    // Test that both product and order are linked.
    $order_item = OrderItem::load(2);
    $product = $order_item->getPurchasedEntity();
    $this->assertNotNull($product);
    $this->assertEquals(3, $product->id());
  }

}
