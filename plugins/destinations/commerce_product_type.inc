<?php

/**
 * @file
 * Support for commerce product types.
 */

/**
 * Destination class implementing migration into commerce product types.
 */
class MigrateDestinationCommerceProductType extends MigrateDestination {
  static public function getKeySchema() {
    return array(
      'type' => array(
        'type' => 'varchar',
        'length' => 32,
        'not null' => TRUE,
        'description' => 'The product type identifier.',
      ),
    );
  }

  /**
   * Returns a list of fields available to be mapped.
   *
   * @return array
   *  Keys: machine names of the fields (to be passed to addFieldMapping)
   *  Values: Human-friendly descriptions of the fields.
   */
  public function fields() {
    $fields = array();
    $fields['type'] = t('Machine name.');
    $fields['name'] = t('Human-readable name.');
    $fields['description'] = t('Brief description');
    $fields['help'] = t('Help text');

    return $fields;
  }

  /**
   * Delete a product type.
   *
   * @param $ids
   *  Array of fields representing the key.
   */
  public function rollback(array $ids) {
    $id = reset($ids);

    migrate_instrument_start('commerce_product_type_delete');
    $this->prepareRollback($id);
    commerce_product_ui_product_type_delete($id);
    $this->completeRollback($id);
    migrate_instrument_stop('commerce_product_type_delete');
    return TRUE;
  }

  /**
   * Import a single product type.
   *
   * @param $product_type
   *  Generic object, refilled with any fields mapped in the Migration.
   * @param $row
   *  Raw source data object - passed through to prepare/complete handlers.
   * @return array
   *  Array of product type machine names if successful. FALSE on failure.
   */
  public function import(stdClass $product_type, stdClass $row) {
    $migration = Migration::currentMigration();
    // Updating previously-migrated content?
    if (isset($row->migrate_map_destid1)) {
      if (isset($product_type->type)) {
        if ($product_type->type != $row->migrate_map_destid1) {
          throw new MigrateException(t("Incoming id !id and map destination id !destid1 don't match",
            array('!id' => $product_type->type, '!destid1' => $row->migrate_map_destid1)));
        }
      }
      else {
        $product_type->type = $row->migrate_map_destid1;
      }
    }

    if ($migration->getSystemOfRecord() == Migration::DESTINATION) {
      if (!isset($product_type->type)) {
        throw new MigrateException(t('System-of-record is DESTINATION, but no destination id provided'));
      }
      // Load the type that's being updated, update its values, then
      // substitute the passed in type with that one.
      $old_product_type = commerce_product_type_load($product_type->type);
      foreach ($product_type as $field => $value) {
        $old_product_type->$field = $product_type->$field;
      }
      $product_type = $old_product_type;
    }

    $this->prepare($product_type, $row);
    migrate_instrument_start('commerce_product_type_save');
    // Convert the object to an array and initialize default values.
    $product_type_array = (array) $product_type;
    $product_type_array += commerce_product_ui_product_type_new();
    $status = commerce_product_ui_product_type_save($product_type_array);
    migrate_instrument_stop('commerce_product_type_save');

    $this->complete($product_type, $row);
    $return = $status ? array($product_type->type) : FALSE;
    return $return;
  }
  
  /**
   * Give handlers a shot at cleaning up before a product type has been rolled back.
   *
   * @param $product_type
   *  Machine name of the product type about to be deleted..
   */
  public function prepareRollback($product_type) {
    $migration = Migration::currentMigration();
    $entity->migrate = array(
      'machineName' => $migration->getMachineName(),
    );

    // Then call any prepare handler for this specific Migration.
    if (method_exists($migration, 'prepareRollback')) {
      $migration->prepareRollback($product_type);
    }
  }

  /**
   * Give handlers a shot at cleaning up after a product type has been rolled back.
   *
   * @param $product_type
   *  Machine name of the product type which has been deleted.
   */
  public function completeRollback($product_type) {
    $migration = Migration::currentMigration();
    // Then call any complete handler for this specific Migration.
    if (method_exists($migration, 'completeRollback')) {
      $migration->completeRollback($product_type);
    }
  }
  
  /**
   * Give handlers a shot at modifying the object before saving it.
   *
   * @param $product_type
   *  Object to build. Prefilled with any fields mapped in the Migration.
   * @param $source_row
   *  Raw source data object - passed through to prepare handlers.
   */
  public function prepare($product_type, stdClass $source_row) {
    $migration = Migration::currentMigration();
    $product_type->migrate = array(
      'machineName' => $migration->getMachineName(),
    );
    
    if (method_exists($migration, 'prepare')) {
      $migration->prepare($product_type, $source_row);
    }
  }

  /**
   * Give handlers a shot at modifying the object (or taking additional action)
   * after saving it.
   *
   * @param $product_type
   *  Object to build. This is the complete object after saving.
   * @param $source_row
   *  Raw source data object - passed through to complete handlers.
   */
  public function complete($product_type, stdClass $source_row) {
    $migration = Migration::currentMigration();
    if (method_exists($migration, 'complete')) {
      $migration->complete($product_type, $source_row);
    }
  }
}
