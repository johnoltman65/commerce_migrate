<?php

namespace Drupal\commerce_migrate\Plugin\migrate\process;

use CommerceGuys\Intl\Calculator;
use Drupal\commerce_order\Adjustment;
use Drupal\commerce_price\Price;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts a nested array into Commerce Adjustments.
 *
 * @MigrateProcessPlugin(
 *   id = "commerce_adjustments",
 *   handle_multiples = TRUE
 * )
 */
class CommerceAdjustments extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_array($value)) {
      $adjustments = [];
      $i = 0;
      foreach ($value as $adjustment) {
        $adjust = [];
        $adjust['delta'] = $i++;
        $adjust['type'] = $adjustment['type'];
        $adjust['label'] = $adjustment['title'];
        $adjustment['amount'] = Calculator::trim($adjustment['amount']);
        $adjust['amount'] = [
          'number' => $adjustment['amount'],
          'currency_code' => $adjustment['currency_code'],
        ];
        $adjust['amount'] = new Price($adjust['amount']['number'], $adjust['amount']['currency_code']);
        $adjustments[] = new Adjustment($adjust);
      }
      return $adjustments;
    }
    return $value;
  }

}
