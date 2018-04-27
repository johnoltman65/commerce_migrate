<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests flat rate shipping migration from Uberart 6.
 *
 * @requires module commerce_shipping
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class ShippingFlatRateTest extends Ubercart6TestBase {

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
    $this->executeMigration('d6_ubercart_shipping_flat_rate');
  }

  /**
   * Test flat rate shipping method migration.
   */
  public function testShippingFlatRate() {
    $type = [
      'id' => '1',
      'label' => 'Fluff Ltd',
      'rate_amount' =>
        [
          'number' => '5.00',
          'currency_code' => 'USD',
        ],
      'stores' => ['1'],
    ];
    $this->assertShippingMethod($type);
    $type = [
      'id' => '2',
      'label' => 'Joopleberry Co.',
      'rate_amount' =>
        [
          'number' => '2.50',
          'currency_code' => 'USD',
        ],
      'stores' => ['1'],
    ];
    $this->assertShippingMethod($type);
  }

}
