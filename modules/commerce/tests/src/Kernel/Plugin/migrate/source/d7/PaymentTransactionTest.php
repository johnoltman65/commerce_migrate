<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Plugin\migrate\source\d7;

use Drupal\Tests\migrate\Kernel\MigrateSqlSourceTestBase;

/**
 * Tests the d7 commerce line item source plugin.
 *
 * @covers \Drupal\commerce_migrate_commerce\Plugin\migrate\source\d7\PaymentTransaction
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class PaymentTransactionTest extends MigrateSqlSourceTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['migrate_drupal', 'commerce_migrate_commerce'];

  /**
   * {@inheritdoc}
   */
  public function providerSource() {
    $tests = [];
    $tests[0]['source_data']['commerce_payment_transaction'] = [
      [
        'transaction_id' => '1',
        'revision_id' => '1',
        'uid' => '1',
        'order_id' => '2',
        'payment_method' => 'commerce_payment_example',
        'instance_id' => 'commerce_payment_example|commerce_payment_commerce_payment_example',
        'remote_id' => NULL,
        'message' => 'Number: @number<br/>Expiration: @month/@year',
        'message_variables' => 'a:3:{s:7:"@number";s:16:"4111111111111111";s:6:"@month";s:2:"06";s:5:"@year";s:4:"2012";}',
        'amount' => '425',
        'currency_code' => 'NZD',
        'status' => 'success',
        'remote_status' => NULL,
        'payload' => 'a:0:{}',
        'created' => '1492868907',
        'changed' => '1498620003',
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
        'transaction_id' => '1',
        'revision_id' => '1',
        'uid' => '1',
        'order_id' => '2',
        'payment_method' => 'commerce_payment_example',
        'instance_id' => 'commerce_payment_example|commerce_payment_commerce_payment_example',
        'remote_id' => NULL,
        'message' => 'Number: @number<br/>Expiration: @month/@year',
        'message_variables' => 'a:3:{s:7:"@number";s:16:"4111111111111111";s:6:"@month";s:2:"06";s:5:"@year";s:4:"2012";}',
        'amount' => '425',
        'currency_code' => 'NZD',
        'status' => 'success',
        'remote_status' => NULL,
        'payload' => 'a:0:{}',
        'created' => '1492868907',
        'changed' => '1498620003',
        'data' => NULL,
      ],
    ];

    return $tests;
  }

}
