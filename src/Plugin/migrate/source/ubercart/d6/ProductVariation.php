<?php

namespace Drupal\commerce_migrate\Plugin\migrate\source\ubercart\d6;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "d6_ubercart_product_variation"
 * )
 */
class ProductVariation extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')->fields('n', [
      'nid', 'vid', 'type', 'title', 'uid', 'created',
      'changed', 'status',
    ]);
    $query->innerJoin('uc_products', 'ucp', 'n.nid = ucp.nid AND n.vid = ucp.vid');
    $query->fields('ucp', ['model', 'sell_price']);
    $query->condition('type', 'product', '=');
    $query->distinct();

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'nid' => $this->t('Node ID'),
      'title' => $this->t('Product title'),
      'model' => $this->t('SKU code'),
      'sell_price' => $this->t('Product price'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
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

}
