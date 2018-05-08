<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc6;

use Drupal\commerce_order\Entity\OrderItem;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests order item migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc6
 */
class OrderItemTest extends Ubercart6TestBase {

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
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installConfig(['commerce_order']);
    $this->installConfig(['commerce_product']);
    $this->migrateStore();
    $this->startCollectingMessages();
    $this->executeMigrations([
      'language',
      'd6_node_type',
      'uc6_product_type',
      'd6_language_content_settings',
      'uc6_language_content_settings',
      'uc6_attribute_field',
      'uc6_product_attribute',
      'uc6_attribute_field_instance',
      'uc6_product_variation',
      'd6_node',
      'uc6_billing_profile',
      'uc6_order_product',
    ]);
  }

  /**
   * Test order item migration.
   */
  public function testOrderItem() {
    $this->assertOrderItem(2, NULL, 3, '1.00', 'Fairy cake', '1500.000000', 'NZD', '1500.000000', 'NZD');
    $this->assertOrderItem(3, NULL, 1, '1.00', 'Bath Towel', '20.000000', 'NZD', '20.000000', 'NZD');
    $this->assertOrderItem(4, NULL, 2, '1.00', 'Beach Towel', '15.000000', 'NZD', '15.000000', 'NZD');

    // Test that both product and order are linked.
    $order_item = OrderItem::load(2);
    $product = $order_item->getPurchasedEntity();
    $this->assertNotNull($product);
    $this->assertEquals(3, $product->id());
  }

}
