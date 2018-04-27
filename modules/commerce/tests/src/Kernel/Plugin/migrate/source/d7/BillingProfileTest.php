<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Plugin\migrate\source\d7;

use Drupal\Tests\migrate\Kernel\MigrateSqlSourceTestBase;

/**
 * Tests the Commerce 1 billing profile source plugin.
 *
 * @covers \Drupal\commerce_migrate_commerce\Plugin\migrate\source\d7\BillingProfile
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class BillingProfileTest extends MigrateSqlSourceTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['migrate_drupal', 'commerce_migrate_commerce'];

  /**
   * {@inheritdoc}
   */
  public function providerSource() {
    $tests = [];
    $tests[0]['source_data']['commerce_customer_profile'] = [
      [
        'profile_id' => '1',
        'revision_id' => '1',
        'type' => 'billing',
        'uid' => '3',
        'status' => '1',
        'created' => '1493287440',
        'changed' => '1493287440',
        'data' => NULL,
      ],
      [
        'profile_id' => '2',
        'revision_id' => '2',
        'type' => 'shipping',
        'uid' => '3',
        'status' => '1',
        'created' => '1493287450',
        'changed' => '1493287450',
        'data' => NULL,
      ],
    ];
    $tests[0]['source_data']['field_config_instance'] = [
      [
        'id' => '2',
        'field_id' => '2',
        'field_name' => 'commerce_unit_price',
        'entity_type' => 'product',
        'bundle' => 'product',
        'data' => 'a:0:{};',
        'deleted' => '0',
      ],
    ];

    // The expected results.
    $tests[0]['expected_data'] = [
      [
        'profile_id' => '1',
        'revision_id' => '1',
        'type' => 'billing',
        'uid' => '3',
        'status' => '1',
        'created' => '1493287440',
        'changed' => '1493287440',
        'data' => NULL,
      ],
    ];

    // Test with commerce_addressbook_default table.
    $tests[1] = $tests[0];
    $tests[1]['source_data']['commerce_addressbook_defaults'] = [
      [
        'cad_id' => '1',
        'profile_id' => '1',
        'type' => 'billing',
        'uid' => '3',
      ],
    ];

    // The expected results.
    $tests[1]['expected_data'][0]['cad_type'] = 'billing';

    return $tests;
  }

}
