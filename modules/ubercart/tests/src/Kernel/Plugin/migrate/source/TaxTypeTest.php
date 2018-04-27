<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Plugin\migrate\source;

use Drupal\Tests\migrate\Kernel\MigrateSqlSourceTestBase;

/**
 * Tests Ubercart tax type source plugin.
 *
 * @covers \Drupal\commerce_migrate_ubercart\Plugin\migrate\source\TaxType
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class TaxTypeTest extends MigrateSqlSourceTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'migrate_drupal',
    'commerce_migrate_ubercart',
  ];

  /**
   * {@inheritdoc}
   */
  public function providerSource() {
    $tests = [];

    // The source data.
    $tests[0]['source_data']['uc_taxes'] = [
      [
        'id' => '1',
        'name' => 'Handling',
        'rate' => '0.05',
        'shippable' => '0',
        'taxed_product_types' => 'a:0:{}',
        'taxed_line_items' => 'a:1:{s:3:"tax";s:3:"tax";',
        'weight' => 0,
      ],
      [
        'id' => '2',
        'name' => 'Fuel',
        'rate' => '0.25',
        'shippable' => '0',
        'taxed_product_types' => 'a:0:{}',
        'taxed_line_items' => 'a:1:{s:3:"tax";s:3:"tax";',
        'weight' => 0,
      ],
    ];

    $tests[0]['expected_data'] = [
      [
        'id' => '1',
        'name' => 'Handling',
        'rate' => '0.05',
      ],
      [
        'id' => '2',
        'name' => 'Fuel',
        'rate' => '0.25',
      ],
    ];
    return $tests;
  }

}
