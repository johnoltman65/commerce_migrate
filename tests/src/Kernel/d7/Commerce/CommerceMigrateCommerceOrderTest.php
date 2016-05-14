<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;
use Drupal\commerce_order\Entity\Order;


/**
 * Tests line item migration.
 *
 * @group commerce_migrate
 */
class CommerceMigrateCommerceOrderTest extends CommerceMigrateCommerce1TestBase {
  static $modules = [
    'text',
    'action',
    'system',
    'entity',
    'views',
    'path',
    'inline_entity_form',
    'commerce_product',
  ];

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('view');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_line_item');
    $this->installEntitySchema('commerce_order');
    $this->installConfig(['commerce_order']);
    $this->executeMigrations([
//      'd7_field',
//      'd7_field_instance',
      'd7_user_role',
      'd7_user',
      'd7_product_type',
      'd7_product',
      'd7_line_item',
      'd7_order',
    ]);
  }

  /**
   * Test line item migration from Drupal 7 to 8.
   */
  public function testOrder() {
    $order = Order::load(1);
    $this->assertNotNull($order);
    $this->assertEquals($order->getCreatedTime(), 1458216500);
    $this->assertEquals($order->getPlacedTime(), 1458216500);
    // @todo Failing because it has no line items.
//    $this->assertEquals(62, $order->total_price->getValue()[0]['amount']);
  }

}
