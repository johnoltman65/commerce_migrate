<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\commerce1;

use Drupal\commerce_order\Entity\OrderItem;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests order item migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce1
 */
class OrderItemTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'commerce_order',
    'commerce_price',
    'commerce_product',
    'commerce_store',
    'path',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->migrateOrderItems();
  }

  /**
   * Test line item migration from Drupal 7 to 8.
   */
  public function testOrderItem() {
    $order = [
      'id' => 1,
      'order_id' => NULL,
      'purchased_entity_id' => '12',
      'quantity' => '1.00',
      'title' => 'Hat 2',
      'unit_price' => '12.000000',
      'unit_price_currency' => 'USD',
      'total_price' => '12.000000',
      'total_price_currency' => 'USD',
      'uses_legacy_adjustments' => '1',
    ];
    $this->assertOrderItem($order['id'], $order['order_id'], $order['purchased_entity_id'], $order['quantity'], $order['title'], $order['unit_price'], $order['unit_price_currency'], $order['total_price'], $order['total_price_currency'], $order['uses_legacy_adjustments']);
    $order = [
      'id' => 2,
      'order_id' => NULL,
      'purchased_entity_id' => '12',
      'quantity' => '1.00',
      'title' => 'Hat 2',
      'unit_price' => '12.000000',
      'unit_price_currency' => 'USD',
      'total_price' => '12.000000',
      'total_price_currency' => 'USD',
      'uses_legacy_adjustments' => '1',
    ];
    $this->assertOrderItem($order['id'], $order['order_id'], $order['purchased_entity_id'], $order['quantity'], $order['title'], $order['unit_price'], $order['unit_price_currency'], $order['total_price'], $order['total_price_currency'], $order['uses_legacy_adjustments']);
    $order = [
      'id' => 3,
      'order_id' => NULL,
      'purchased_entity_id' => '45',
      'quantity' => '1.00',
      'title' => 'Tshirt 3',
      'unit_price' => '38.000000',
      'unit_price_currency' => 'USD',
      'total_price' => '38.000000',
      'total_price_currency' => 'USD',
      'uses_legacy_adjustments' => '1',
    ];
    $this->assertOrderItem($order['id'], $order['order_id'], $order['purchased_entity_id'], $order['quantity'], $order['title'], $order['unit_price'], $order['unit_price_currency'], $order['total_price'], $order['total_price_currency'], $order['uses_legacy_adjustments']);
    $order = [
      'id' => 11,
      'order_id' => NULL,
      'purchased_entity_id' => NULL,
      'quantity' => '1.00',
      'title' => 'Express shipping: 1 business day',
      'unit_price' => '15.000000',
      'unit_price_currency' => 'USD',
      'total_price' => '15.000000',
      'total_price_currency' => 'USD',
      'uses_legacy_adjustments' => '1',
    ];
    $this->assertOrderItem($order['id'], $order['order_id'], $order['purchased_entity_id'], $order['quantity'], $order['title'], $order['unit_price'], $order['unit_price_currency'], $order['total_price'], $order['total_price_currency'], $order['uses_legacy_adjustments']);
    $order = [
      'id' => 12,
      'order_id' => NULL,
      'purchased_entity_id' => NULL,
      'quantity' => '1.00',
      'title' => 'Free shipping: 5 - 8 business days',
      'unit_price' => '0.000000',
      'unit_price_currency' => 'USD',
      'total_price' => '0.000000',
      'total_price_currency' => 'USD',
      'uses_legacy_adjustments' => '1',
    ];
    $this->assertOrderItem($order['id'], $order['order_id'], $order['purchased_entity_id'], $order['quantity'], $order['title'], $order['unit_price'], $order['unit_price_currency'], $order['total_price'], $order['total_price_currency'], $order['uses_legacy_adjustments']);
    $order = [
      'id' => 13,
      'order_id' => NULL,
      'purchased_entity_id' => NULL,
      'quantity' => '1.00',
      'title' => 'Express shipping: 1 business day',
      'unit_price' => '1.500000',
      'unit_price_currency' => 'USD',
      'total_price' => '1.500000',
      'total_price_currency' => 'USD',
      'uses_legacy_adjustments' => '1',
    ];
    $this->assertOrderItem($order['id'], $order['order_id'], $order['purchased_entity_id'], $order['quantity'], $order['title'], $order['unit_price'], $order['unit_price_currency'], $order['total_price'], $order['total_price_currency'], $order['uses_legacy_adjustments']);
    $order = [
      'id' => 14,
      'order_id' => NULL,
      'purchased_entity_id' => 10,
      'quantity' => '3.00',
      'title' => 'Water Bottle 1',
      'unit_price' => '16.000000',
      'unit_price_currency' => 'USD',
      'total_price' => '48.000000',
      'total_price_currency' => 'USD',
      'uses_legacy_adjustments' => '1',
    ];
    $this->assertOrderItem($order['id'], $order['order_id'], $order['purchased_entity_id'], $order['quantity'], $order['title'], $order['unit_price'], $order['unit_price_currency'], $order['total_price'], $order['total_price_currency'], $order['uses_legacy_adjustments']);

    // Test time stamps.
    $order_item = OrderItem::load(1);
    $this->assertEquals($order_item->getCreatedTime(), 1493287435);
    $this->assertEquals($order_item->getChangedTime(), 1493287440);
    $order_item = OrderItem::load(2);
    $this->assertEquals($order_item->getCreatedTime(), 1493287445);
    $this->assertEquals($order_item->getChangedTime(), 1493287450);
    $order_item = OrderItem::load(3);
    $this->assertEquals($order_item->getCreatedTime(), 1493287455);
    $this->assertEquals($order_item->getChangedTime(), 1493287460);
  }

}
