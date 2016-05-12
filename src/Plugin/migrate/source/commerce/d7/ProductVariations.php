<?php

namespace Drupal\commerce_migrate\Plugin\migrate\source\commerce\d7;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;

/**
 * Drupal 7 commerce_line_item source from database.
 *
 * @MigrateSource(
 *   id = "d7_product",
 *   source = "commerce_product"
 * )
 */

class ProductVariations extends FieldableEntity {
  public function fields() {
    return [
      'product_id' => t('Product (variation) ID'),
      'sku' => t('SKU'),
      'title' => t('Title'),
      'type' => t('Type'),
//      'quantity' => t('Quantity'),
      'created' => t('Created'),
      'changed' => t('Changes'),
      'data' => t('Data'),
    ];
  }

  /**
   * @inheritDoc
   */
  public function getIds() {
    $ids['product_id']['type'] = 'integer';
    $ids['product_id']['alias'] = 'p';

    return $ids;
  }

  /**
   * @inheritDoc
   */
  public function query() {
    $query = $this->select('commerce_product', 'p')
      ->fields('p', array_keys($this->fields()));

    return $query;
  }

  /**
   * @inheritDoc
   */
  public function prepareRow(Row $row) {
    // Get Field API field values.
    foreach (array_keys($this->getFields('commerce_product', $row->getSourceProperty('type'))) as $field) {
      $line_item_id = $row->getSourceProperty('product_id');
      $revision_id = $row->getSourceProperty('revision_id');
      $row->setSourceProperty($field, $this->getFieldValues('commerce_product', $field, $line_item_id, $revision_id));
    }
    return parent::prepareRow($row);
  }
}
