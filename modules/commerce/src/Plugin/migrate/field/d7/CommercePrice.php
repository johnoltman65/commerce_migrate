<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\field\d7;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\field\FieldPluginBase;

/**
 * Field migration for commerce_price.
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

}
