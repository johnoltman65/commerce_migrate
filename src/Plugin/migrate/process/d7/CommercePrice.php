<?php

namespace Drupal\commerce_migrate\Plugin\migrate\process\d7;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Migrate commerce price from non-decimal to decimal storage.
 *
 * @MigrateProcessPlugin(
 *   id = "commerce_migrate_commerce_price"
 * )
 */
class CommercePrice extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Import the currency if needed, and load it.
    /** @var \Drupal\commerce_price\Entity\CurrencyInterface $currency */
    $currency = \Drupal::service('commerce_price.currency_importer')->import($value['currency_code']);

    // Convert the amount to decimal per the currency's specification.
    if (!is_float($value['amount'])) {
      $negative = (bccomp('0', $value['amount'], 12) == 1);
      $signMultiplier = $negative ? '-1' : '1';
      $value['amount'] = bcdiv($value['amount'], $signMultiplier, $currency->getFractionDigits());
    }

    return $value;
  }

}
