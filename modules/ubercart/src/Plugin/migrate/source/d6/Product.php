<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\d6;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d6\Node;

/**
 * Drupal 6 ubercart product source.
 *
 * @MigrateSource(
 *   id = "d6_ubercart_product",
 *   source_module = "uc_product"
 * )
 */
class Product extends Node {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();
    $query->innerJoin('uc_products', 'ucp', 'n.nid = ucp.nid AND n.vid = ucp.vid');
    $query->fields('ucp', ['model', 'sell_price']);
    if (isset($this->configuration['node_type'])) {
      $query->condition('n.type', $this->configuration['node_type']);
    }
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'nid' => $this->t('Node ID'),
      'uid' => $this->t('User ID of person who added product'),
      'title' => $this->t('Product name'),
      'body' => $this->t('Product description'),
      'status' => $this->t('Published status'),
      'created' => $this->t('Date product created'),
      'changed' => $this->t('Last time product changed'),
      'teaser' => $this->t('Product teaser'),
      'model' => $this->t('Product model'),
      'sell_price' => $this->t('Sell price of the product'),
      'name' => $this->t('Filter name'),
      'stores' => $this->t('Stores'),
    ];
    return parent::fields() + $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $row->setSourceProperty('stores', 1);
    $row->setSourceProperty('name', str_replace(' ', '_', strtolower($row->getSourceProperty('name'))));
    return parent::prepareRow($row);
  }

}
