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
    $this->installEntitySchema('profile');
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

    // Test the order
    $this->assertNotNull($order);
    $this->assertEquals($order->getOrderNumber(), 1);
    $this->assertEquals($order->getCreatedTime(), 1458216500);
    $this->assertEquals($order->getPlacedTime(), 1458216500);

    // Test line items
    $line_items = $order->getLineItems();
    $this->assertNotNull($line_items);
    $this->assertEquals('TSH3-LTB-MD', $line_items[0]->label());
    $this->assertEquals('TSH1-BLK-SM', $line_items[1]->label());
    $this->assertEquals(62, $order->total_price->getValue()[0]['amount']);

    // Test billing profile.
    $profile = $order->getBillingProfile();
    $this->assertNotNull($profile);
    $this->assertEquals($profile->getType(), 'billing');
    $this->assertEquals($profile->isActive(), TRUE);
  }

}
