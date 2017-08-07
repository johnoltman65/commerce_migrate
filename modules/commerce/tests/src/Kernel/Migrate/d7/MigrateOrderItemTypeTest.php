<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\commerce_order\Entity\OrderItemType;

/**
 * Tests order item type migration.
 *
 * @group migrate_drupal_7
 */
class MigrateOrderItemTypeTest extends Commerce1TestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->executeMigration('d7_commerce_order_item_type');
  }

  /**
   * Tests the Drupal 6 taxonomy vocabularies to Drupal 8 migration.
   */
  public function testOrderItemType() {
    $this->assertOrderItemType('product', "product");
  }

  /**
   * Asserts an order item type configuration entity.
   *
   * @param string $id
   *   The order item type id.
   * @param string $expected_label
   *   The expected label.
   */
  public function assertOrderItemType($id, $expected_label) {
    $order_item_type = OrderItemType::load($id);
    $this->assertInstanceOf(OrderItemType::class, $order_item_type);
    $this->assertSame($expected_label, $order_item_type->label());
  }

}
