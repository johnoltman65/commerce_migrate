<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\source\d7;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\MigrateException;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Drupal 7 commerce_order source from database.
 *
 * @MigrateSource(
 *   id = "d7_order",
 *   source = "order"
 * )
 */
class Order extends FieldableEntity {

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
      throw new MigrateException('You must have a store saved in order to import orders.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'order_id' => t('Order ID'),
      'order_number' => t('Order Number'),
      'revision_id' => t('Revision ID'),
      'type' => t('Type'),
      'uid' => t('User ID'),
      'mail' => t('Email'),
      'status' => t('Status'),
      'created' => t('Created'),
      'changed' => t('Changed'),
      'hostname' => t('Hostname'),
      'data' => t('Data'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['order_id']['type'] = 'integer';
    $ids['order_id']['alias'] = 'ord';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('commerce_order', 'ord')
      ->fields('ord', array_keys($this->fields()));

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Get Field API field values.
    foreach (array_keys($this->getFields('commerce_order', $row->getSourceProperty('type'))) as $field) {
      $nid = $row->getSourceProperty('order_id');
      $vid = $row->getSourceProperty('revision_id');
      $row->setSourceProperty($field, $this->getFieldValues('commerce_order', $field, $nid, $vid));
    }

    $row->setDestinationProperty('type', 'default');
    $row->setSourceProperty('type', 'default');

    $row->setDestinationProperty('store_id', $this->defaultStore->id());

    return parent::prepareRow($row);
  }

}
