<?php

/**
 * @file
 *   Commerce Product Type migration.
 */

class CommerceMigrateUbercartProductTypeMigration extends Migration {
  public function __construct() {
    parent::__construct();
    $this->description = t('Import product types from Ubercart.');

    // Create a map object for tracking the relationships between source rows
    $this->map = new MigrateSQLMap($this->machineName,
        array(
          'pcid' => array('type' => 'varchar',
                           'length' => 32,
                           'not null' => TRUE,
                           'description' => 'The node type identifier.',
                          ),
        ),
        MigrateDestinationCommerceProductType::getKeySchema()
      );

    // Create a MigrateSource object, which manages retrieving the input data.
    $query = db_select('uc_product_classes', 'ucp')
              ->fields('ucp', array('pcid', 'name', 'description'));
    $this->source = new MigrateSourceSQL($query);
    $this->destination = new MigrateDestinationCommerceProductType();

    $this->addFieldMapping('type', 'pcid');
    $this->addFieldMapping('name', 'name');
    $this->addFieldMapping('description', 'description');
  }
  
  function prepare($type, stdClass $row) {
    // Based on this, the save function selects between an INSERT and an UPDATE.
    // Done here instead of in the plugin so that an UPDATE could be done by changing
    // the line bellow.
    $type->is_new = TRUE;
  }
  
  /**
   * A new type has been created, register the appropriate migrations.
   */
  public function complete($entity, stdClass $row) {
    $type = $entity->type;
    $product_migration = 'CommerceMigrateUbercartProduct' . ucfirst($type);
    $node_migration = 'CommerceMigrateUbercartProductNode' . ucfirst($type);
    Migration::registerMigration('CommerceMigrateUbercartProductMigration', $product_migration, array('type' => $type));
    Migration::registerMigration('CommerceMigrateUbercartProductNodeMigration', $node_migration, array('type' => $type));
  }
  
  /** 
   * The type has been deleted, deregister the migration.
   */
  public function completeRollback($type) {
    $product_migration = 'CommerceMigrateUbercartProduct' . ucfirst($type);
    $node_migration = 'CommerceMigrateUbercartProductNode' . ucfirst($type);
    Migration::deregisterMigration($product_migration);
    Migration::deregisterMigration($node_migration);
  }
}
