<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Plugin\migrate\source\d7;

use Drupal\Tests\migrate\Kernel\MigrateSqlSourceTestBase;

/**
 * Tests the d7 message source plugin.
 *
 * @covers \Drupal\commerce_migrate_commerce\Plugin\migrate\source\d7\Message
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class MessageTest extends MigrateSqlSourceTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['migrate_drupal', 'commerce_migrate_commerce'];

  /**
   * {@inheritdoc}
   */
  public function providerSource() {
    $tests = [];
    $tests[0]['source_data']['message'] = [
      [
        'mid' => '2',
        'type' => 'commerce_order_created',
        'arguments' => 'a:1:{s:14:"!order-summary";a:2:{s:8:"callback";s:30:"commerce_message_order_summary";s:12:"pass message";b:1;}}',
        'uid' => '3',
        'timestamp' => '1492868907',
        'language' => 'en',
      ],
    ];
    $tests[0]['source_data']['message_type'] = [
      [
        'id' => '2',
        'name' => 'commerce_order_created',
        'category' => 'commerce_order_message',
        'description' => 'Commerce Order: created',
        'argument_keys' => 'a:0:{}',
        'language' => NULL,
        'status' => '2',
        'module' => 'commerce_message',
        'arguments' => 'N;',
        'data' => 'a:1:{s:5:"purge";a:4:{s:8:"override";i:0;s:7:"enabled";i:0;s:5:"quota";s:0:"";s:4:"days";s:0:"";}}',
      ],
    ];
    $tests[0]['source_data']['field_config_instance'] = [
      [
        'id' => '2',
        'field_id' => '2',
        'field_name' => 'commerce_unit_price',
        'entity_type' => 'commerce_line_item',
        'bundle' => 'product',
        'data' => 'a:0:{};',
        'deleted' => '0',
      ],
      [
        'id' => '3',
        'field_id' => '3',
        'field_name' => 'message_commerce_order',
        'entity_type' => 'message',
        'bundle' => 'commerce_order_created',
        'data' => 'a:6:{s:5:"label";s:5:"Order";s:8:"required";b:1;s:6:"widget";a:4:{s:4:"type";s:28:"entityreference_autocomplete";s:6:"module";s:15:"entityreference";s:8:"settings";a:3:{s:14:"match_operator";s:8:"CONTAINS";s:4:"size";s:2:"60";s:4:"path";s:0:"";}s:6:"weight";i:0;}s:8:"settings";a:1:{s:18:"user_register_form";b:0;}s:7:"display";a:1:{s:7:"default";a:5:{s:5:"label";s:5:"above";s:4:"type";s:21:"entityreference_label";s:8:"settings";a:1:{s:4:"link";b:0;}s:6:"module";s:15:"entityreference";s:6:"weight";i:0;}}s:11:"description";s:0:"";}',
        'deleted' => '0',
      ],
    ];
    $tests[0]['source_data']['field_data_message_commerce_order'] = [
      [
        'entity_type' => 'message',
        'bundle' => 'commerce_order_created',
        'deleted' => '0',
        'entity_id' => '2',
        'revision_id' => 'product',
        'language' => 'und',
        'delta' => 0,
        'message_commerce_order_target_id' => 1,
      ],
    ];

    // The expected results.
    $tests[0]['expected_data'] = [
      [
        'mid' => '2',
        'type' => 'commerce_order_created',
        'arguments' => 'a:1:{s:14:"!order-summary";a:2:{s:8:"callback";s:30:"commerce_message_order_summary";s:12:"pass message";b:1;}}',
        'uid' => '3',
        'timestamp' => '1492868907',
        'language' => 'en',
        'category' => 'commerce_order_message',
        'name' => 'commerce_order_created',
        'target_id' => 1,
      ],
    ];

    return $tests;
  }

}
