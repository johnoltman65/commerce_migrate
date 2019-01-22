<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Plugin\migrate\source;

/**
 * Tests the Ubercart order product source plugin without currency data.
 *
 * @covers \Drupal\commerce_migrate_ubercart\Plugin\migrate\source\OrderProduct
 * @group commerce_migrate_uc
 */
class OrderProductNoCurrencyTest extends OrderProductCurrencyTest {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['migrate_drupal', 'commerce_migrate_ubercart'];

  /**
   * {@inheritdoc}
   */
  public function providerSource() {
    $tests = parent::providerSource();

    foreach ($tests as $test) {
      unset($test['source_data']['uc_orders']['currency']);
    }
    return $tests;
  }

}
