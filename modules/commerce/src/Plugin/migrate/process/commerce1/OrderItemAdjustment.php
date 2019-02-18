<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\process\commerce1;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Builds an array of adjustment data.
 *
 * @MigrateProcessPlugin(
 *   id = "commerce1_order_item_adjustment"
 * )
 */
class OrderItemAdjustment extends CommercePrice {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $adjustment = [];

    // Having no adjustments is valid, so no logging or MigrateSkipRowException
    // needed here.
    if (is_array($value)) {
      if ($value['name'] !== 'base_price') {
        $parts = explode('|', $value['name'], -1);
        if ($parts) {
          $percentage = isset($value['percentage']) ? $value['percentage'] : NULL;
          $amount = (string) $value['price']['amount'];
          $currency_code = $value['price']['currency_code'];
          if ($parts[0] === 'discount') {

            // Scale the incoming price by the fraction digits.
            $fraction_digits = isset($value['price']['fraction_digits']) ? $value['price']['fraction_digits']['fraction_digits'] : '2';
            $input = [
              'amount' => $amount,
              'fraction_digits' => $fraction_digits,
              'currency_code' => $currency_code,
            ];

            $price_scaled = parent::transform($input, $migrate_executable, $row, NULL);

            $type = 'promotion';
            $label = $value['price']['data']['discount_component_title'];

            $adjustment = [
              'type' => $type,
              'label' => $label,
              'amount' => $price_scaled['number'],
              'currency_code' => $currency_code,
              'percentage' => $percentage,
              'sourceId' => 'custom',
              'included' => FALSE,
              'locked' => TRUE,
            ];
          }
        }
      }
    }
    return $adjustment;
  }

}
