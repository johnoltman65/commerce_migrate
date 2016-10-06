<?php

namespace Drupal\commerce_migrate\Plugin\migrate\process\d7;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\Migration;
use Drupal\migrate\Row;

/**
 * Migrate reference fields.
 *
 * @MigrateProcessPlugin(
 *   id = "commerce_migrate_commerce_reference_revision"
 * )
 */
class CommerceReferenceRevision extends Migration {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $ids = parent::transform($value, $migrate_executable, $row, $destination_property);
    $target_id = $ids[0];
    $revision_id = $ids[1];
    return ['target_id' => $target_id, 'target_revision_id' => $revision_id];
  }

}
