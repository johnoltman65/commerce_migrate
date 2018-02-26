<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\source\d7;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Gets Commerce 1 payment gateway from database.
 *
 * @MigrateSource(
 *   id = "d7_commerce_payment_gateway",
 *   source_module = "commerce_payment"
 * )
 */
class PaymentGateway extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('commerce_payment_transaction', 'cpt')
      ->distinct()
      ->fields('cpt', ['payment_method']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'payment_method' => $this->t('Payment method'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'payment_method' => [
        'type' => 'string',
      ],
    ];
  }

}
