<?php

/**
 * @file
 *   Commerce Product Display Node migration.
 *   This is a dynamic migration, reused for every product type
 *   (so that products of each type can be imported separately)
 *   @todo Migrate the product image.
 *   @todo Do we want a node type per ubercart product class?
 */

class CommerceMigrateUbercartProductNodeMigration extends DynamicMigration {
  public function __construct(array $arguments) {
    $this->arguments = $arguments;
    parent::__construct();
    $this->description = t('Import product nodes from Ubercart.');
    //$dependency_name = 'CommerceMigrateUbercartProduct' . ucfirst($arguments['type']);
    //$this->dependencies = array($dependency_name);

    // Create a map object for tracking the relationships between source rows
    $this->map = new MigrateSQLMap($this->machineName,
      array(
        'nid' => array(
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'description' => 'Ubercart node ID',
        ),
      ),
      MigrateDestinationNode::getKeySchema()
    );

    // Create a MigrateSource object, which manages retrieving the input data.
    $query = db_select('node', 'n');
    $query->leftJoin('field_data_body', 'fdb', 'n.nid = entity_id');
    $query->fields('n', array('nid', 'uid', 'type', 'title', 'created', 'changed'))
          ->fields('fdb', array('body_value', 'body_summary', 'body_format', 'language'))
          ->condition('n.type', $arguments['type'])
          ->distinct();
    $this->source = new MigrateSourceSQL($query);
    // The product_display content type is created by commerce install profiles,
    // not the module itself, so hardcoding this might be unwise.
    $this->destination = new MigrateDestinationNode('product_display');

    // Properties
    $this->addFieldMapping('uid', 'uid');
    $this->addFieldMapping('title', 'title');
    $this->addFieldMapping('created', 'created');
    $this->addFieldMapping('changed', 'changed');
    
    // Fields
    // We have no idea of which type the referenced product is, so
    // we specify all (for the sourceMigration).
    $this->addFieldMapping('field_product', 'nid')
         ->sourceMigration($this->getProductMigrations());
  
    $arguments = MigrateTextFieldHandler::arguments(array('source_field' => 'body_summary'), 
                                                    array('source_field' => 'body_format'), 
                                                    array('source_field' => 'language'));
    $this->addFieldMapping('body', 'body_value')
         ->arguments($arguments); 
  }
  
  /**
   * Return a list of all product migrations.
   */
  protected function getProductMigrations() {
    $migrations = array();
    foreach (commerce_product_types() as $type => $product_type) {
      $migrations[] = 'CommerceMigrateUbercartProduct' . ucfirst($type);
    }
    return $migrations;
  }
  
  /**
   * Construct the machine name (identifying the migration in "drush ms" and other places).
   */
  protected function generateMachineName($class_name = NULL) {
    return 'CommerceMigrateUbercartProductNode' . ucfirst($this->arguments['type']); 
  }
}
