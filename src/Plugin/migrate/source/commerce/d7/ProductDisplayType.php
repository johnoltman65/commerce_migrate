<?php

namespace Drupal\commerce_migrate\Plugin\migrate\source\commerce\d7;

use Drupal\migrate\MigrateException;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 7 commerce_product_type source from database.
 *
 * @MigrateSource(
 *   id = "d7_product_display_type",
 *   source = "product"
 * )
 */
class ProductDisplayType extends DrupalSqlBase {

  /**
   * @inheritDoc
   */
  public function fields() {
    return [
      'field_name' => t('Product reference field name'),
      'type' => t('Type'),
      'name' => t('Name'),
      'description' => t('Description'),
      'help' => t('Help'),
      'data' => t('Product reference field instance data'),
    ];
  }

  /**
   * @inheritDoc
   */
  public function getIds() {
    $ids['type']['type'] = 'string';
    $ids['type']['alias'] = 'nt';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $instance_config = unserialize($row->getSourceProperty('data'));
    $product_variation_type = array_filter($instance_config['settings']['referenceable_types']);

    if (count($product_variation_type) > 1) {
      // @todo Come up with some form of resolution.
      throw new MigrateException("Currently only 1:1 node display to product type migrations work.");
    }

    $product_variation_type = reset($product_variation_type);

    $row->setSourceProperty('variation_type', $product_variation_type);

    return parent::prepareRow($row);
  }

  /**
   * @inheritDoc
   */
  public function query() {
    $query = $this->select('field_config', 'fc');
    $query->leftJoin('field_config_instance', 'fci', '(fci.field_id = fc.id)');
    $query->leftJoin('node_type', 'nt', '(nt.type = fci.bundle)');
    $query->condition('fc.type', 'commerce_product_reference')
      ->condition('fc.active', 1)
      ->condition('fci.entity_type', 'node')
      ->condition('nt.disabled', 0);
    $query->fields('fc', ['field_name'])
      ->fields('fci', ['data'])
      ->fields('nt', ['type', 'name', 'description', 'help']);
    return $query;
  }

}
