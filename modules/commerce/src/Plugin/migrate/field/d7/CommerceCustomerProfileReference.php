<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\field\d7;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_drupal\Plugin\migrate\field\FieldPluginBase;

/**
 * Field migration for the Customer Profile Reference field.
 *
 * @MigrateField(
 *   id = "commerce_customer_profile_reference",
 *   type_map = {
 *     "commerce_customer_profile_reference" = "entity_reference"
 *   },
 *   core = {7},
 *   source_module = "commerce_customer",
 *   destination_module = "profile"
 * )
 */
class CommerceCustomerProfileReference extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function processFieldValues(MigrationInterface $migration, $field_name, $data) {
    $process = [
      'plugin' => 'iterator',
      'source' => $field_name,
      'process' => [
        'target_id' => 'profile_id',
      ],
    ];
    $migration->setProcessOfProperty($field_name, $process);
  }

}
