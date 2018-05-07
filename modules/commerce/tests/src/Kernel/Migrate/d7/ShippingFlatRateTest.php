<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests flat rate shipping migration from Commerce 1.
 *
 * @requires module commerce_shipping
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class ShippingFlatRateTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'path',
    'commerce_product',
    'commerce_shipping',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('commerce_shipping_method');
    $this->executeMigration('d7_commerce_shipping_flat_rate');
  }

  /**
   * Test flat rate shipping method migration.
   */
  public function testShippingFlatRate() {
    $type = [
      'id' => '1',
      'label' => 'Express Shipping',
      'rate_amount' =>
        [
          'number' => '15.00',
          'currency_code' => 'USD',
        ],
      'stores' => ['1'],
    ];
    $this->assertShippingMethod($type);
    $type = [
      'id' => '2',
      'label' => 'Free Shipping',
      'rate_amount' =>
        [
          'number' => '0.00',
          'currency_code' => 'USD',
        ],
      'stores' => ['1'],
    ];
    $this->assertShippingMethod($type);
    $type = [
      'id' => '3',
      'label' => 'Standard Shipping',
      'rate_amount' =>
        [
          'number' => '8.00',
          'currency_code' => 'USD',
        ],
      'stores' => ['1'],
    ];
    $this->assertShippingMethod($type);
  }

}
