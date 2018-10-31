<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\source\commerce1;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;

/**
 * Drupal 7 commerce_customer_profile source from database.
 *
 * @MigrateSource(
 *   id = "commerce1_profile",
 *   source_module = "profile"
 * )
 */
class Profile extends FieldableEntity {

  /**
   * The join options between the node and the node_revisions table.
   */
  const JOIN = 'cp.revision_id = cpr.revision_id';

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('commerce_customer_profile', 'cp')
      ->fields('cp');

    /** @var \Drupal\Core\Database\Schema $db */
    if ($this->getDatabase()->schema()->tableExists('commerce_addressbook_defaults')) {
      $query->leftJoin('commerce_addressbook_defaults', 'cad', 'cp.profile_id = cad.profile_id AND cp.uid = cad.uid AND cp.type = cad.type');
      $query->addField('cad', 'type', 'cad_type');
    }
    else {
      // If the currency column does not exist, add it as an expression to
      // normalize the query results.
      $query->addExpression(':cad', 'cad_type', [':cad' => FALSE]);
    }

    if (isset($this->configuration['profile_type'])) {
      $types = is_array($this->configuration['profile_type']) ? $this->configuration['profile_type'] : [$this->configuration['profile_type']];
      $query->condition('cp.type', $types, 'IN');
    }
    return $query;
  }

  /**
   * {@inheritdoc}
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
      'cad_type' => $this->t('Type, if matching entry in defaults table'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $profile_id = $row->getSourceProperty('profile_id');
    $revision_id = $row->getSourceProperty('revision_id');
    // Get Field API field values.
    foreach (array_keys($this->getFields('commerce_customer_profile', $row->getSourceProperty('type'))) as $field) {
      $row->setSourceProperty($field, $this->getFieldValues('commerce_customer_profile', $field, $profile_id, $revision_id));
    }
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['profile_id']['type'] = 'integer';
    $ids['profile_id']['alias'] = 'cp';
    return $ids;
  }

}
