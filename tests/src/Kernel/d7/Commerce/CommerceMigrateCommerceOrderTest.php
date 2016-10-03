<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;

use Drupal\commerce_order\Entity\Order;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate
 */
class CommerceMigrateCommerceOrderTest extends CommerceMigrateCommerce1TestBase {

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
    $this->createDefaultStore();
    // @todo Execute the d7_field and d7_field_instance migrations?
    $this->executeMigrations([
      'd7_user_role',
      'd7_user',
      'd7_product_type',
      'd7_product',
      'd7_billing_profile',
      'd7_line_item',
      'd7_order',
    ]);
  }

  /**
   * Test line item migration from Drupal 7 to 8.
   */
  public function testOrder() {
    $order = Order::load(1);

    // Test the order.
    $this->assertNotNull($order);
    $this->assertEquals($order->getOrderNumber(), 1);
    $this->assertEquals($order->getCreatedTime(), 1458216500);
    $this->assertEquals($order->getPlacedTime(), 1458216500);

    // Test line items.
    $order_items = $order->getItems();
    $this->assertNotNull($order_items);
    $this->assertEquals('TSH3-LTB-MD', $order_items[0]->label());
    $this->assertEquals('TSH1-BLK-SM', $order_items[1]->label());
    $this->assertEquals(62, $order->getTotalPrice()->getNumber());

    // Test billing profile.
    $profile = $order->getBillingProfile();
    $this->assertNotNull($profile);
    $this->assertEquals($profile->getType(), 'billing');
    $this->assertEquals($profile->isActive(), TRUE);

    // Test store.
    $this->assertEquals(\Drupal::service('commerce_store.default_store_resolver')->resolve()->id(), $order->getStoreId());
  }

  /**
   * Tests various order migration states.
   */
  public function testOrderStates() {
    $order_draft = Order::load(1);
    $order_pending = Order::load(2);
    $order_complete = Order::load(3);
    $order_canceled = Order::load(4);

    $this->assertEquals('Draft', $order_draft->getState()->getLabel()->render());
    $this->assertEquals('Completed', $order_pending->getState()->getLabel()->render());
    $this->assertEquals('Completed', $order_complete->getState()->getLabel()->render());
    $this->assertEquals('Canceled', $order_canceled->getState()->getLabel()->render());
  }

}
