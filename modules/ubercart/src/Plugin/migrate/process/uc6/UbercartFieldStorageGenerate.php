<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\process\uc6;

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Create field storage.
 *
 * For use with d7_field, this plugin allows field storage to be created on
 * two entities while processing a row.
 *
 * @MigrateProcessPlugin(
 *   id = "ubercart_field_storage_generate"
 * )
 */
class UbercartFieldStorageGenerate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($value) {
      $field_name = $row->getSourceProperty('field_name');
      $entity_type = $value;
      $name = $entity_type . '.' . $field_name;
      if (!FieldStorageConfig::load($name)) {
        $field_storage_definition = [
          'field_name' => $field_name,
          'entity_type' => $entity_type,
          'type' => $row->getDestinationProperty('type'),
          'cardinality' => $row->getDestinationProperty('cardinality'),
          'settings' => $row->getDestinationProperty('settings'),
        ];
        $storage = FieldStorageConfig::create($field_storage_definition);
        $storage->save();
      }
    }
  }

}
