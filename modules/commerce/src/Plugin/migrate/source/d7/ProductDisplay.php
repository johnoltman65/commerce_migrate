<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\source\d7;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;

/**
 * Drupal 7 commerce_line_item source from database.
 *
 * @MigrateSource(
 *   id = "d7_product_display",
 *   source = "commerce_product"
 * )
 */
class ProductDisplay extends FieldableEntity {

  /**
   * The default store.
   *
   * @var \Drupal\commerce_store\Entity\StoreInterface
   */
  protected $defaultStore;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityManagerInterface $entity_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state, $entity_manager);

    $this->defaultStore = \Drupal::service('commerce_store.default_store_resolver')->resolve();
    if (!$this->defaultStore) {
      throw new MigrateException('You must have a store saved in order to import products.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => t('Product (variation) ID'),
      'title' => t('Title'),
      'type' => t('Type'),
      'uid' => t('Owner'),
      'status' => t('Status'),
      'created' => t('Created'),
      'changed' => t('Changes'),
      'field_name' => t('Field name for variations'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['nid']['type'] = 'integer';
    $ids['nid']['alias'] = 'n';

    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n');
    $query->leftJoin('field_config_instance', 'fci', '(n.type = fci.bundle)');
    $query->leftJoin('field_config', 'fc', '(fc.id = fci.field_id)');
    $query->condition('fc.type', 'commerce_product_reference');
    $query->fields('n', [
      'nid',
      'title',
      'type',
      'uid',
      'status',
      'created',
      'changed',
    ]);
    $query->fields('fc', ['field_name']);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $variations_field_name = $row->getSourceProperty('field_name');
    // Get Field API field values.
    foreach (array_keys($this->getFields('node', $row->getSourceProperty('type'))) as $field) {
      $nid = $row->getSourceProperty('nid');
      $vid = $row->getSourceProperty('vid');

      // If this is the product reference field, map it to `variations_field`
      // since it does not have a standardized name.
      if ($field == $variations_field_name) {
        $row->setSourceProperty('variations_field', $this->getFieldValues('node', $variations_field_name, $nid, $vid));
      }
      else {
        $row->setSourceProperty($field, $this->getFieldValues('node', $field, $nid, $vid));
      }
    }

    $row->setDestinationProperty('stores', [
      'target_id' => $this->defaultStore->id(),
    ]);

    return parent::prepareRow($row);
  }

}
