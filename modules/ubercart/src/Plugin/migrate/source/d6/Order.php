<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\d6;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Provides migration source for orders.
 *
 * @MigrateSource(
 *   id = "d6_ubercart_order",
 *   source_module = "uc_order"
 * )
 */
class Order extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('uc_orders', 'uo')
      ->fields('uo');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'order_id' => $this->t('Order ID'),
      'uid' => $this->t('User ID of order'),
      'order_status' => $this->t('Order status'),
      'primary_email' => $this->t('Email associated with order'),
      'host' => $this->t('IP address of customer'),
      'data' => $this->t('Order attributes'),
      'created' => $this->t('Date/time of order creation'),
      'modified' => $this->t('Date/time of last order modification'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // The Migrate API automatically serializes arrays for storage in longblob
    // fields so we unserialize them here.
    $data = unserialize($row->getSourceProperty('data'));
    // Ubercart 6 stores credit card information in a hash. Since this probably
    // isn't necessary so I removed it here.
    unset($data['cc_data']);
    $row->setSourceProperty('data', $data);

    $order_id = $row->getSourceProperty('order_id');
    $query = $this->select('uc_order_products', 'uop')
      ->fields('uop', ['order_product_id'])
      ->condition('order_id', $order_id, '=');
    $results = $query->execute()->fetchCol();
    $row->setSourceProperty('order_item_ids', $results);

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'order_id' => [
        'type' => 'integer',
        'alias' => 'uo',
      ],
    ];
  }

}
