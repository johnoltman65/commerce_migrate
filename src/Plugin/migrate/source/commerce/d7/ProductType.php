<?php

namespace Drupal\commerce_migrate\Plugin\migrate\source\commerce\d7;

use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 7 commerce_product_type source from database.
 *
 * @MigrateSource(
 *   id = "d7_product_type",
 *   source = "product"
 * )
 */
class ProductType extends DrupalSqlBase {

  /**
   * @inheritDoc
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
   * @inheritDoc
   */
  public function getIds() {
    $ids['type']['type'] = 'varchar';
    $ids['type']['alias'] = 'pt';
    return $ids;
  }

  /**
   * @inheritDoc
   */
  public function query() {
    $query = $this->select('commerce_product_type', 'pt')
      ->fields('pt', array_keys($this->fields()));
    return $query;
  }
}