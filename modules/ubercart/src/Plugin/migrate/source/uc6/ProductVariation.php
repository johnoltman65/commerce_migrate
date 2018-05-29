<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\uc6;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d6\Node;

/**
 * Ubercart 6  product variation source.
 *
 * @MigrateSource(
 *   id = "uc6_product_variation",
 *   source_module = "uc_product"
 * )
 */
class ProductVariation extends Node {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();
    $query->innerJoin('uc_products', 'ucp', 'n.nid = ucp.nid AND n.vid = ucp.vid');
    $query->fields('ucp', ['model', 'sell_price']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'vid' => t('The primary identifier for this version.'),
      'log' => $this->t('Revision Log message'),
      'uid' => $this->t('Node UID.'),
      'model' => $this->t('SKU code'),
      'sell_price' => $this->t('Product price'),
    ];
    return parent::fields() + $fields;
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
