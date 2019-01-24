<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\process\commerce1;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Builds an array of adjustment data.
 *
 * @MigrateProcessPlugin(
 *   id = "commerce1_order_adjustment_shipping"
 * )
 */
class OrderAdjustmentShipping extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $adjustment = [];

    if (!isset($value['data']['shipping_service']['base_rate'])) {
      throw new MigrateSkipRowException(sprintf("Adjustment does not have a base rate '%s'", $destination_property));
    }
    $base_rate = $value['data']['shipping_service']['base_rate'];

    if (!isset($base_rate['amount'])) {
      throw new MigrateSkipRowException("Adjustment base rate amount does not exists for destination '%s'", $destination_property);
    }

    if (!isset($base_rate['currency_code'])) {
      throw new MigrateSkipRowException("Adjustment base rate currency code does not exists for destination '%s'", $destination_property);
    }

    if (is_array($value)) {
      $fraction_digits = isset($base_rate['fraction_digits']) ? $base_rate['fraction_digits'] : '2';

      // Scale the incoming price by the fraction digits.
      $input = [
        'amount' => $base_rate['amount'],
        'fraction_digits' => $fraction_digits,
        'currency_code' => $base_rate['currency_code'],
      ];
      $price = new CommercePrice([], 'price', '');
      $price_scaled = $price->transform($input, $migrate_executable, $row, NULL);

      $adjustment = [
        'type' => 'shipping',
        'label' => isset($value['line_item_label']) ? $value['line_item_label'] : 'Shipping',
        'amount' => $price_scaled['number'],
        'currency_code' => $price_scaled['currency_code'],
        'source_id' => 'custom',
        'included' => FALSE,
        'locked' => TRUE,
      ];
    }
    return $adjustment;
  }

}
