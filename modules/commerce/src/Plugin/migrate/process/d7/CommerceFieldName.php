<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\process\d7;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Calculate the field name.
 *
 * @MigrateProcessPlugin(
 *   id = "commerce_field_name"
 * )
 */
class CommerceFieldName extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $field_name = $row->getSourceProperty('field_name');
    // Get the commerce attribute style field name.
    if (($row->getSourceProperty('entity_type') == 'commerce_product') &&
      ($row->getSourceProperty('type') == 'taxonomy_term_reference')) {
      $instances = $row->getSourceProperty('instances');
      // If any instance has a select widget, then this is an attribute.
      foreach ($instances as $instance) {
        $data = unserialize(($instance['data']));
        if ($data['widget']['type'] == 'options_select') {
          $field_name = $row->getDestinationProperty('field_name_attribute');
          break;
        }
      }
    }
    return $field_name;
  }

}
