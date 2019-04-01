<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Provides migration source for orders.
 *
 * @MigrateSource(
 *   id = "uc_order",
 *   source_module = "uc_order"
 * )
 */
class Order extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('uc_orders', 'uo')->fields('uo');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'order_id' => $this->t('Order ID'),
      'uid' => $this->t('User ID of order'),
      'order_status' => $this->t('Order status'),
      'primary_email' => $this->t('Email associated with order'),
      'host' => $this->t('IP address of customer'),
      'data' => $this->t('Order attributes'),
      'created' => $this->t('Date/time of order creation'),
      'modified' => $this->t('Date/time of last order modification'),
      'order_item_ids' => $this->t('Order item IDs'),
      'refresh_state' => $this->t('Order refresh state'),
      'adjustments' => $this->t('Order adjustments'),
      'comment_id' => $this->t('OrderComments id'),
      'message' => $this->t('Changed timestamp'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Add refresh skip value to the row.
    $row->setSourceProperty('refresh_state', OrderInterface::REFRESH_SKIP);
    $data = unserialize($row->getSourceProperty('data'));
    // Ubercart 6 stores credit card information in a hash. Since this probably
    // isn't necessary so I removed it here.
    unset($data['cc_data']);
    $row->setSourceProperty('data', $data);

    // Puts product order ids for this order in the row.
    $order_id = $row->getSourceProperty('order_id');
    $query = $this->select('uc_order_products', 'uop')
      ->fields('uop', ['order_product_id'])
      ->condition('order_id', $order_id, '=');
    $results = $query->execute()->fetchCol();
    $row->setSourceProperty('order_item_ids', $results);

    // Both uc_order_admin_comments and uc_order_comments are created on
    // install of ubercart uc_order module.
    $order_id = $row->getSourceProperty('order_id', $order_id);
    $query = $this->select('uc_order_admin_comments', 'uoac')->fields('uoac')
      ->condition('order_id', $order_id);
    $results = $query->execute()->fetchAll();
    $value = [];
    $i = 0;
    foreach ($results as $result) {
      $value[$i]['value'] = $result['message'];
      $value[$i++]['format'] = NULL;
    }
    $row->setSourceProperty('order_admin_comments', $value);

    $query = $this->select('uc_order_comments', 'uoc')->fields('uoc')
      ->condition('order_id', $order_id);
    $results = $query->execute()->fetchAll();
    $value = [];
    $i = 0;
    foreach ($results as $result) {
      $value[$i]['value'] = $result['message'];
      $value[$i++]['format'] = NULL;
    }
    $row->setSourceProperty('order_comments', $value);

    $row->setSourceProperty('adjustments', $this->getAdjustmentData($row));
    return parent::prepareRow($row);
  }

  /**
   * Retrieves adjustment data for an order.
   *
   * @param \Drupal\migrate\Row $row
   *   The row.
   *
   * @return array
   *   The field values, keyed by delta.
   */
  protected function getAdjustmentData(Row $row) {
    $order_id = $row->getSourceProperty('order_id');
    $query = $this->select('uc_order_line_items', 'uol')
      ->fields('uol')
      ->fields('uo', ['order_id'])
      ->orderBy('weight', 'ASC')
      ->condition('uol.order_id', $order_id);
    $query->innerJoin('uc_orders', 'uo', 'uol.order_id = uo.order_id');
    $adjustments = $query->execute()->fetchAll();

    $currency_code = $this->variableGet('uc_currency_code', 'USD');
    foreach ($adjustments as &$adjustment) {
      $adjustment['currency_code'] = $currency_code;
      $adjustment['type'] = 'custom';
    }
    return $adjustments;
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
