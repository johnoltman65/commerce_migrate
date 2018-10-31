<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc7;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests order item migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc7
 */
class OrderItemTest extends Ubercart7TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'commerce_order',
    'commerce_price',
    'commerce_product',
    'commerce_store',
    'migrate_plus',
    'node',
    'path',
    'profile',
    'state_machine',
    'telephone',
    'text',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->migrateOrderItems();
  }

  /**
   * Test order item migration.
   */
  public function testOrderItem() {
    $this->assertOrderItem(1, NULL, 2, '1.00', 'Romulan ale', '100.000000', 'USD', '100.000000', 'USD', '1');
    $this->assertOrderItem(2, NULL, 2, '4.00', 'Romulan ale', '100.100000', 'USD', '400.400000', 'USD', '1');
    $this->assertOrderItem(3, NULL, 3, '1.00', 'Holosuite 1', '40.000000', 'USD', '40.000000', 'USD', '1');
    $this->assertOrderItem(4, NULL, 1, '1.00', 'Breshtanti ale', '50.500000', 'USD', '50.500000', 'USD', '1');
  }

}
