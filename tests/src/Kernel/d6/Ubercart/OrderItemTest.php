<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\commerce_order\Entity\OrderItem;

/**
 * Tests order item migration.
 *
 * @group commerce_migrate
 */
class OrderItemTest extends Ubercart6TestBase {

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
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installConfig(['commerce_order']);
    $this->installConfig(['commerce_product']);
    $this->migrateStore();
    $this->startCollectingMessages();
    $this->executeMigrations([
      'd6_ubercart_billing_profile',
      'd6_ubercart_order',
      'd6_ubercart_product_variation',
      'd6_ubercart_product',
      'd6_ubercart_order_product',
    ]);
  }

  /**
   * Test order item migration from Drupal 6 to 8.
   */
  public function testOrderItem() {
    $order_item = OrderItem::load(2);
    $this->assertNotNull($order_item);

    $this->assertEquals(1, $order_item->getQuantity());
    $this->assertEquals('Fairy cake', $order_item->getTitle());

    // Test that both product and order are linked.
    $product = $order_item->getPurchasedEntity();
    $this->assertNotNull($product);
    $this->assertEquals(3, $product->id());

    $order = $order_item->getOrder();
    $this->assertNotNull($order);
    $this->assertEquals(2, $order->id());
  }

}
