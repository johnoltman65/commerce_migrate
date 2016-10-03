<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\commerce_order\Entity\LineItem;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate
 */
class LineItemTest extends Ubercart6TestBase {

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
      'd6_ubercart_product_variation',
      'd6_ubercart_product',
      'd6_ubercart_order_product',
    ]);
  }

  /**
   * Test line item migration from Drupal 6 to 8.
   */
  public function testLineItem() {
    $line_item = LineItem::load(126);
    $this->assertNotNull($line_item);

    $this->assertEquals(5, $line_item->getQuantity());
    $this->assertEquals('Product 28', $line_item->getTitle());

    // Test that both product and order are linked.
    $product = $line_item->getPurchasedEntity();
    $this->assertNotNull($product);
    $this->assertEquals(2, $product->id());

    $order = $line_item->getOrder();
    $this->assertNotNull($order);
    $this->assertEquals(78, $order->id());
  }

}
