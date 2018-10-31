<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\source\commerce1;

/**
 * Drupal 7 commerce_customer_profile revision source from database.
 *
 * @MigrateSource(
 *   id = "commerce1_profile_revision",
 *   source_module = "profile"
 * )
 */
class ProfileRevision extends Profile {

  /**
   * The join options between the node and the node_revisions table.
   */
  const JOIN = 'cp.profile_id = cpr.profile_id AND cp.revision_id <> cpr.revision_id';

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return parent::fields() + [
      'timestamp' => $this->t('Revision timestamp'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['revision_id']['type'] = 'integer';
    $ids['revision_id']['alias'] = 'cp';
    return $ids;
  }

}
