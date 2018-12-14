<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc7;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests order item migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc7
 */
class OrderItemTest extends Ubercart7TestBase {

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
    'node',
    'path',
    'profile',
    'state_machine',
    'telephone',
    'text',
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
      'id' => 1,
      'order_id' => NULL,
      'purchased_entity_id' => 2,
      'quantity' => '1.00',
      'title' => 'Romulan ale',
      'unit_price' => '100.000000',
      'unit_price_currency_code' => 'USD',
      'total_price' => '100.000000',
      'total_price_currency_code' => 'USD',
      'uses_legacy_adjustments' => '1',
      'adjustments' => [],
    ];
    $this->assertOrderItem($order_item);

    $order_item = [
      'id' => 2,
      'order_id' => NULL,
      'purchased_entity_id' => 2,
      'quantity' => '4.00',
      'title' => 'Romulan ale',
      'unit_price' => '100.100000',
      'unit_price_currency_code' => 'USD',
      'total_price' => '400.400000',
      'total_price_currency_code' => 'USD',
      'uses_legacy_adjustments' => '1',
      'adjustments' => [],
    ];
    $this->assertOrderItem($order_item);

    $order_item = [
      'id' => 3,
      'order_id' => NULL,
      'purchased_entity_id' => 3,
      'quantity' => '1.00',
      'title' => 'Holosuite 1',
      'unit_price' => '40.000000',
      'unit_price_currency_code' => 'USD',
      'total_price' => '40.000000',
      'total_price_currency_code' => 'USD',
      'uses_legacy_adjustments' => '1',
      'adjustments' => [],
    ];
    $this->assertOrderItem($order_item);

    $order_item = [
      'id' => 4,
      'order_id' => NULL,
      'purchased_entity_id' => 1,
      'quantity' => '1.00',
      'title' => 'Breshtanti ale',
      'unit_price' => '50.500000',
      'unit_price_currency_code' => 'USD',
      'total_price' => '50.500000',
      'total_price_currency_code' => 'USD',
      'uses_legacy_adjustments' => '1',
      'adjustments' => [],
    ];
    $this->assertOrderItem($order_item);

  }

}
