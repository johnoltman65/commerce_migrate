<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\uc6;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Ubercart 6  product variation source.
 *
 * @MigrateSource(
 *   id = "uc6_product_variation",
 *   source_module = "uc_product"
 * )
 */
class ProductVariation extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')->fields('n', [
      'nid',
      'vid',
      'type',
      'title',
      'uid',
      'created',
      'changed',
      'status',
    ]);
    $query->innerJoin('uc_products', 'ucp', 'n.nid = ucp.nid AND n.vid = ucp.vid');
    $query->fields('ucp', ['model', 'sell_price']);
    $query->distinct();
    if (isset($this->configuration['node_type'])) {
      $query->condition('n.type', $this->configuration['node_type']);
    }
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return ([
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Node revision ID'),
      'title' => $this->t('Product title'),
      'type' => $this->t('Product type'),
      'uid' => $this->t('Node UID.'),
      'status' => $this->t('Node status'),
      'created' => $this->t('Node created time'),
      'changed' => $this->t('Node changed time'),
      'model' => $this->t('SKU code'),
      'sell_price' => $this->t('Product price'),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Set the currency for each variation based on Ubercart global setting.
    $row->setSourceProperty('currency', $this->getUbercartCurrency());
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }

  /**
   * Gets the Ubercart global currency.
   *
   * @return string
   *   The currency.
   */
  public function getUbercartCurrency() {
    static $currency;

    if (empty($currency)) {
      $currency = $this->variableGet('uc_currency_code', 'USD');
    }
    return $currency;
  }

}
