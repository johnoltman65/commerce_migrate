<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\source\d7;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 7 commerce_product_type source from database.
 *
 * @MigrateSource(
 *   id = "d7_product_type",
 *   source_module = "commerce_product"
 * )
 */
class ProductType extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'type' => t('Type'),
      'name' => t('Name'),
      'description' => t('Description'),
      'help' => t('Help'),
      'revision' => t('Revision'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['type']['type'] = 'string';
    $ids['type']['alias'] = 'pt';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('commerce_product_type', 'pt')
      ->fields('pt', array_keys($this->fields()));
  }

}
