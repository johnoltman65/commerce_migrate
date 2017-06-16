<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\commerce_order\Entity\Order;

/**
 * Tests billing profile migration.
 *
 * @group commerce_migrate
 */
class OrderTest extends Ubercart6TestBase {

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
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = Order::load(1);
    $this->assertNotNull($order);
    $this->assertEquals(1492868907, $order->getCreatedTime());
    $this->assertEquals(1493078815, $order->getChangedTime());
    $this->assertEquals('fordprefect@example.com', $order->getEmail());
    $this->assertEquals('validation', $order->getState()->getLabel());
    $this->assertNotNull($order->getBillingProfile());
    // @todo This regressed with beta1 fixes?
    // $data = $order->getData();
    // $this->assertFalse(isset($data['cc_data']));
  }

}
