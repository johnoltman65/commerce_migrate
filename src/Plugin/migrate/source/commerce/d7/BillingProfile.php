<?php

namespace Drupal\commerce_migrate\Plugin\migrate\source\commerce\d7;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;

/**
 * Drupal 7 commerce_customer_profile source from database.
 *
 * @MigrateSource(
 *   id = "d7_billing_profile",
 *   source_provider = "commerce_customer"
 * )
 */
class BillingProfile extends FieldableEntity {

  /**
   * The join options between the node and the node_revisions table.
   */
  const JOIN = 'cp.revision_id = cpr.revision_id';

  /**
   * @inheritDoc
   */
  public function fields() {
    return [
      'profile_id' => $this->t('Profile ID'),
      'revision_id' => $this->t('Revision ID'),
      'type' => $this->t('Type'),
      'uid' => $this->t('Owner'),
      'status' => $this->t('Status'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
      'data' => $this->t('Data blob'),
    ];
  }

  /**
   * @inheritDoc
   */
  public function getIds() {
    $ids['profile_id']['type'] = 'integer';
    $ids['profile_id']['alias'] = 'cp';
  }

  /**
   * @inheritDoc
   */
  public function query() {
    $query = $this->select('commerce_customer_profile_revision', 'cpr')
      ->fields('cp', array_keys($this->fields()));
    $query->innerJoin('commerce_customer_profile', 'cp', static::JOIN);
    $query->condition('type', 'billing');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Get Field API field values.
    foreach (array_keys($this->getFields('node', $row->getSourceProperty('type'))) as $field) {
      $nid = $row->getSourceProperty('nid');
      $vid = $row->getSourceProperty('vid');
      $row->setSourceProperty($field, $this->getFieldValues('node', $field, $nid, $vid));
    }
    return parent::prepareRow($row);
  }
}
