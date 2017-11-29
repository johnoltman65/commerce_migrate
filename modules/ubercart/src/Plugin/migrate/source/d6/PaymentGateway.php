<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\d6;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Drupal 6 ubercart payment gateway source.
 *
 * Migrate the Drupal 6 payment methods to a manual payment gateway.
 *
 * @MigrateSource(
 *   id = "d6_ubercart_payment_gateway",
 *   source_module = "uc_payment"
 * )
 */
class PaymentGateway extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('uc_payment_receipts', 'upr')
      ->distinct()
      ->fields('upr', ['method']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'method' => $this->t('Payment method'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'method' => [
        'type' => 'string',
        'alias' => 'upr',
      ],
    ];
  }

}
