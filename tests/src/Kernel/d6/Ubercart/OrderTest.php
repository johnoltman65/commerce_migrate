<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\commerce_order\Entity\Order;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests billing profile migration.
 *
 * @group commerce_migrate
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
    $this->assertOrder(1, '1', '1', '1492868907', '1493078815', 'fordprefect@example.com', 'validation', '10.1.1.2', '3', '1493078815');
    $this->assertOrder(2, '2', '1', '1492989920', '1493081092', 'trintragula@example.com', 'validation', '10.1.1.2', '5', '1493081092');

    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = Order::load(1);
    $this->assertNotNull($order->getBillingProfile());
    $this->assertNull($order->getData('cc_data'));  $order = Order::load(2);
    $this->assertNotNull($order->getBillingProfile());
    $this->assertNull($order->getData('cc_data'));
  }

}
