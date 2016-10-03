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
    $this->installEntitySchema('commerce_line_item');
    $this->installEntitySchema('commerce_order');
    $this->installConfig(['commerce_order']);
    $this->createDefaultStore();
    $this->startCollectingMessages();
    $this->executeMigrations([
      'd6_filter_format',
      'd6_user_role',
      'd6_user',
      'd6_ubercart_billing_profile',
      'd6_ubercart_order',
    ]);

  }

  /**
   * Test order migration from Drupal 6 to 8.
   */
  public function testOrder() {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = Order::load(78);
    $this->assertNotNull($order);
    $this->assertEquals(1306876624, $order->getCreatedTime());
    $this->assertEquals(1306876784, $order->getChangedTime());
    $this->assertEquals('maeva.slawa@example.com', $order->getEmail());
    $this->assertEquals('Draft', $order->getState()->getLabel());
    $this->assertNotNull($order->getBillingProfile());
    $data = $order->getData();
    $this->assertFalse(isset($data['cc_data']), 'Saved credit card information was removed');
  }

}
