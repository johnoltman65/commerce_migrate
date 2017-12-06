<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\field\d7;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_drupal\Plugin\migrate\field\FieldPluginBase;

/**
 * Field migration for commerce_line item.
 *
 * @MigrateField(
 *   id = "commerce_line_item_reference",
 *   type_map = {
 *     "commerce_line_item_reference" = "entity_reference"
 *   },
 *   core = {7},
 *   source_module = "commerce_line_item",
 *   destination_module = "commerce_order"
 * )
 */
class CommerceLineItemReference extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function processFieldValues(MigrationInterface $migration, $field_name, $data) {
    $process = [
      'plugin' => 'iterator',
      'source' => $field_name,
      'process' => [
        'target_id' => 'order_item_id',
      ],
    ];
    $migration->setProcessOfProperty($field_name, $process);
  }

}
