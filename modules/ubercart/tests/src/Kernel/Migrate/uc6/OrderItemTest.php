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
    $order_item = [
      'id' => 2,
      'order_id' => NULL,
      'purchased_entity_id' => 3,
      'quantity' => '1.00',
      'title' => 'Fairy cake',
      'unit_price' => '1500.000000',
      'unit_price_currency_code' => 'NZD',
      'total_price' => '1500.000000',
      'total_price_currency_code' => 'NZD',
      'uses_legacy_adjustments' => '1',
      'adjustments' => [],
    ];
    $this->assertOrderItem($order_item);

    $order_item = [
      'id' => 3,
      'order_id' => NULL,
      'purchased_entity_id' => 1,
      'quantity' => '1.00',
      'title' => 'Bath Towel',
      'unit_price' => '20.000000',
      'unit_price_currency_code' => 'NZD',
      'total_price' => '20.000000',
      'total_price_currency_code' => 'NZD',
      'uses_legacy_adjustments' => '1',
      'adjustments' => [],
    ];
    $this->assertOrderItem($order_item);

    $order_item = [
      'id' => 4,
      'order_id' => NULL,
      'purchased_entity_id' => 2,
      'quantity' => '1.00',
      'title' => 'Beach Towel',
      'unit_price' => '15.000000',
      'unit_price_currency_code' => 'NZD',
      'total_price' => '15.000000',
      'total_price_currency_code' => 'NZD',
      'uses_legacy_adjustments' => '1',
      'adjustments' => [],
    ];
    $this->assertOrderItem($order_item);

    // Test that both product and order are linked.
    $order_item = OrderItem::load(2);
    $product = $order_item->getPurchasedEntity();
    $this->assertNotNull($product);
    $this->assertEquals(3, $product->id());
  }

}
