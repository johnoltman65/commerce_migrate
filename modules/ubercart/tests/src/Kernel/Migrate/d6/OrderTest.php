<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\commerce_order\Entity\Order;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests order migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class OrderTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

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
    $this->installEntitySchema('profile');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installConfig(['commerce_order']);
    $this->migrateStore();
    $this->executeMigrations([
      'd6_ubercart_billing_profile',
      'd6_ubercart_order',
    ]);

  }

  /**
   * Test order migration from Drupal 6 to 8.
   */
  public function testOrder() {
    $order = [
      'id' => 1,
      'number' => '1',
      'store_id' => '1',
      'created_time' => '1492868907',
      'changed_time' => '1493078815',
      'completed_time' => '1493078815',
      'email' => 'fordprefect@example.com',
      'ip_address' => '10.1.1.2',
      'customer_id' => '3',
      'placed_time' => '1493078815',
      'adjustments' => [],
      'label_value' => 'validation',
      'label_rendered' => 'validation',
    ];
    $this->assertOrder($order);
    $order = [
      'id' => 2,
      'number' => '2',
      'store_id' => '1',
      'created_time' => '1492989920',
      'changed_time' => '1508916762',
      'completed_time' => '1508916762',
      'email' => 'trintragula@example.com',
      'label' => 'completed',
      'ip_address' => '10.1.1.2',
      'customer_id' => '5',
      'placed_time' => '1508916762',
      'adjustments' => [],
      'label_value' => 'completed',
      'label_rendered' => 'Completed',
    ];
    $this->assertOrder($order);
    $order = [
      'id' => 3,
      'number' => '3',
      'store_id' => '1',
      'created_time' => '1511148641',
      'changed_time' => '1511149246',
      'completed_time' => '1511149246',
      'email' => 'zaphod@example.com',
      'label' => 'completed',
      'ip_address' => '10.1.1.2',
      'customer_id' => '4',
      'placed_time' => '1511149246',
      'adjustments' => [],
      'label_value' => 'completed',
      'label_rendered' => 'Completed',
    ];
    $this->assertOrder($order);
    $order = [
      'id' => 4,
      'number' => '4',
      'store_id' => '1',
      'created_time' => '1502996811',
      // Changed time is overwritten by Commerce when the status is Draft. The
      // source changed time is '1502996997'.
      'changed_time' => NULL,
      'completed_time' => NULL,
      'email' => 'trillian@example.com',
      'label' => 'completed',
      'ip_address' => '127.0.0.1',
      'customer_id' => '2',
      'placed_time' => '1502996997',
      'adjustments' => [],
      'label_value' => 'draft',
      'label_rendered' => 'Draft',
    ];
    $this->assertOrder($order);

    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = Order::load(1);
    $this->assertNotNull($order->getBillingProfile());
    $this->assertNull($order->getData('cc_data'));
    $order = Order::load(2);
    $this->assertNotNull($order->getBillingProfile());
    $this->assertNull($order->getData('cc_data'));
  }

}
