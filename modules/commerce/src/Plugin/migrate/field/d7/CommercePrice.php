<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\field\d7;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\field\FieldPluginBase;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Commerce price migrate field plugin.
 *
 * @MigrateField(
 *   id = "commerce_price",
 *   core = {7},
 *   source_module = "commerce_price",
 *   destination_module = "commerce_price"
 * )
 */
class CommercePrice extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFieldType(Row $row) {
    return 'commerce_price';
  }

    /**
     * {@inheritdoc}
     */
  public function processFieldValues(MigrationInterface $migration, $field_name, $data) {
    $destination_field_name = $field_name;
    if ($field_name == 'commerce_unit_price') {
      $destination_field_name = 'unit_price';
    }
    elseif ($field_name == 'commerce_total') {
      $destination_field_name = 'total_price';
    }
    $process = [
      'plugin' => 'commerce_migrate_commerce_price',
      'source' => $field_name,
    ];

    $migration->setProcessOfProperty($destination_field_name, $process);
  }

}
