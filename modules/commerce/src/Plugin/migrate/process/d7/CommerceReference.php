<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\process\d7;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Migrate reference fields.
 *
 * @MigrateProcessPlugin(
 *   id = "commerce_migrate_commerce_reference"
 * )
 */
class CommerceReference extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return $value['target_id'] = $value[$this->configuration['target_key']];
  }

}
