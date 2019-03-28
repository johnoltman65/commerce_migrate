<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc7;

use Drupal\commerce_order\Adjustment;
use Drupal\commerce_price\Price;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests order migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc7
 */
class OrderTest extends Ubercart7TestBase {

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
    'filter',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->migrateOrders();
  }

  /**
   * Test order migration.
   */
  public function testUbercartOrder() {
    $order = [
      'id' => 1,
      'type' => 'default',
      'number' => '1',
      'store_id' => '1',
      'created_time' => '1493326662',
      'changed_time' => '1536901828',
      'completed_time' => NULL,
      'email' => 'tomparis@example.com',
      'ip_address' => '172.19.0.2',
      'customer_id' => '2',
      'placed_time' => '1536901828',
      'total_price' => '112.000000',
      'total_price_currency' => 'USD',
      'label_value' => 'validation',
      'label_rendered' => 'validation',
      'order_items_ids' => ['1'],
      'billing_profile' => ['1', '1'],
      'cart' => NULL,
      'data' => [],
      'adjustments' => [
        new Adjustment([
          'type' => 'custom',
          'label' => 'Shipping',
          'amount' => new Price('2', 'USD'),
          'percentage' => NULL,
          'source_id' => 'custom',
          'included' => FALSE,
          'locked' => TRUE,
        ]),
        new Adjustment([
          'type' => 'custom',
          'label' => 'Station maintenance',
          'amount' => new Price('5', 'USD'),
          'percentage' => NULL,
          'source_id' => 'custom',
          'included' => FALSE,
          'locked' => TRUE,
        ]),
      ],
      'order_admin_comments' => [
        [
          'value' => 'Order created through website.',
          'format' => NULL,
        ],
        [
          'value' => 'Admin comment 1',
          'format' => NULL,
        ],
        [
          'value' => 'Admin comment 2',
          'format' => NULL,
        ],
      ],
      'order_comments' => [],
    ];
    $this->assertUbercartOrder($order);

    $order = [
      'id' => 2,
      'type' => 'default',
      'number' => '2',
      'store_id' => '1',
      'created_time' => '1536901552',
      'changed_time' => '1536963792',
      'completed_time' => '1536963792',
      'email' => 'harrykim@example.com',
      'label' => 'completed',
      'ip_address' => '172.19.0.2',
      'customer_id' => '4',
      'placed_time' => '1536963792',
      'total_price' => '440.400000',
      'total_price_currency' => 'USD',
      'label_value' => 'completed',
      'label_rendered' => 'Completed',
      'order_items_ids' => ['2', '3'],
      'billing_profile' => ['2', '2'],
      'cart' => NULL,
      'data' => [],
      'adjustments' => [],
      'order_admin_comments' => [
        [
          'value' => 'Order created by the administration.',
          'format' => NULL,
        ],
      ],
      'order_comments' => [],
    ];
    $this->assertUbercartOrder($order);

    $order = [
      'id' => 3,
      'type' => 'default',
      'number' => '3',
      'store_id' => '1',
      'created_time' => '1536902338',
      'changed_time' => '1536964646',
      'completed_time' => NULL,
      'email' => 'tomparis@example.com',
      'label' => 'completed',
      'ip_address' => '172.19.0.2',
      'customer_id' => '2',
      'placed_time' => '1536964646',
      'total_price' => NULL,
      'total_price_currency' => 'USD',
      'label_value' => 'validation',
      'label_rendered' => 'validation',
      'order_items_ids' => [],
      'billing_profile' => ['1', '3'],
      'cart' => NULL,
      'data' => [],
      'adjustments' => [],
      'order_admin_comments' => [
        [
          'value' => 'Order created by the administration.',
          'format' => NULL,
        ],
      ],
      'order_comments' => [],
    ];
    $this->assertUbercartOrder($order);

    $order = [
      'id' => 4,
      'type' => 'default',
      'number' => '4',
      'store_id' => '1',
      'created_time' => '1536902428',
      'changed_time' => '1536902428',
      'completed_time' => NULL,
      'email' => 'harrykim@example.com',
      'label' => 'completed',
      'ip_address' => '127.0.0.1',
      'customer_id' => '4',
      'placed_time' => '1536902428',
      'total_price' => '50.500000',
      'total_price_currency' => 'USD',
      'label_value' => 'validation',
      'label_rendered' => 'validation',
      'order_items_ids' => ['4'],
      'billing_profile' => ['2', '4'],
      'cart' => NULL,
      'data' => [],
      'adjustments' => [],
      'order_admin_comments' => [
        [
          'value' => 'Order created by the administration.',
          'format' => NULL,
        ],
      ],
      'order_comments' => [],
    ];
    $this->assertUbercartOrder($order);
  }

}
