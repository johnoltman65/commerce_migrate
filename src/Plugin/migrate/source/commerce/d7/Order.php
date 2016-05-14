<?php

namespace Drupal\commerce_migrate\Plugin\migrate\source\commerce\d7;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;

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
   * @inheritDoc
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
   * @inheritDoc
   */
  public function getIds() {
    $ids['order_id']['type'] = 'integer';
    $ids['order_id']['alias'] = 'ord';
    return $ids;
  }

  /**
   * @inheritDoc
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

    return parent::prepareRow($row);
  }
}
