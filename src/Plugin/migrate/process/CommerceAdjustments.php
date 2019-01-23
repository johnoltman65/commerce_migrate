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
 *   handle_multiples = true
 * )
 */
class CommerceAdjustments extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_array($value) && !empty($value)) {
      $adjustments = [];
      $i = 0;
      foreach ($value as $adjustment) {
        if ($adjustment) {
          $adjust = [];
          $adjust['delta'] = $i++;
          $adjust['type'] = $adjustment['type'];
          $adjust['label'] = isset($adjustment['label']) ? $adjustment['label'] : $adjustment['title'];
          $adjust['amount'] = [
            'number' => Calculator::trim($adjustment['amount']),
            'currency_code' => $adjustment['currency_code'],
          ];
          $adjust['amount'] = new Price($adjust['amount']['number'], $adjust['amount']['currency_code']);
          $adjust['percentage'] = isset($adjustment['percentage']) ? $adjustment['percentage'] : NULL;
          $adjust['sourceId'] = isset($adjustment['source_id']) ? $adjustment['source_id'] : 'custom';
          $adjust['included'] = isset($adjustment['included']) ? $adjustment['included'] : FALSE;
          $adjust['locked'] = isset($adjustment['locked']) ? $adjustment['locked'] : TRUE;
          $adjustments[] = new Adjustment($adjust);
        }
      }
      return $adjustments;
    }
    return $value;
  }

}
