<?php

/**
 * @file
 *   Commerce Product migration.
 *   This is a dynamic migration, reused for every product type
 *   (so that products of each type can be imported separately)
 *   @todo Migrate the product image.
 */

class CommerceMigrateUbercartProductMigration extends DynamicMigration {
  public function __construct(array $arguments) {
    $this->arguments = $arguments;
    parent::__construct();
    $this->description = t('Import products from Ubercart.');
    $this->dependencies = array('CommerceMigrateUbercartProductType');

    // Create a map object for tracking the relationships between source rows
    $this->map = new MigrateSQLMap($this->machineName,
      array(
          'nid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'description' => 'Ubercart node ID',
            'alias' => 'ucp',
          ),
      ),
      MigrateDestinationEntityAPI::getKeySchema('commerce_product', $arguments['type'])
    );
    // Create a MigrateSource object, which manages retrieving the input data.
    $query = db_select('node', 'n');
    $query->innerJoin('uc_products', 'ucp', 'n.nid = ucp.nid AND n.vid = ucp.vid');
    $query->fields('n', array('nid', 'vid', 'type', 'title', 'created', 'changed'))
          ->fields('ucp', array('model', 'sell_price'))
          ->condition('n.type', $arguments['type'])
          ->distinct();
    $this->source = new MigrateSourceSQL($query);
    $this->destination = new MigrateDestinationEntityAPI('commerce_product', $arguments['type']);

    // Properties
    $this->addFieldMapping('sku', 'model');
    $this->addFieldMapping('type', 'type')
      ->sourceMigration('CommerceMigrateUbercartProductType');
    $this->addFieldMapping('title', 'title');
    $this->addFieldMapping('created', 'created');
    $this->addFieldMapping('changed', 'changed');
    $this->addFieldMapping('uid', 'uid');
    $this->addFieldMapping('status', 'status');
    $this->addFieldMapping('product_id')->issueGroup(t('DNM'));
    $this->addFieldMapping('path')->issueGroup(t('DNM'));
    // Fields
    $arguments = MigrateCommercePriceFieldHandler::arguments(array('USD'));
    $this->addFieldMapping('commerce_price', 'sell_price')
      ->arguments($arguments);
    $this->addFieldMapping('field_image','images')
      ->arguments(array('file_function' => 'file_fid'));
  }

  public function prepareRow($row) {
    // If the dollar currency is disabled, enable it.
    $enabled_currencies = variable_get('commerce_enabled_currencies', array('USD' => 'USD'));
    if (empty($enabled_currencies['USD'])) {
      $enabled_currencies['USD'] = 'USD';
      variable_set('commerce_enabled_currencies', $enabled_currencies);
    }

    // Prepare the imagefields, from http://drupal.org/node/1159234
    $query = db_select('field_data_uc_product_image', 'img');
    $query->fields('img', array('uc_product_image_fid'))
      ->condition('img.entity_id', $row->nid)
      ->condition('img.revision_id', $row->vid);

    $result = $query->execute();
    foreach ($result as $image_row) {
      $row->images[] = $image_row->uc_product_image_fid;
    }
  }

  /**
   * Construct the machine name (identifying the migration in "drush ms" and other places).
   */
  protected function generateMachineName($class_name = NULL) {
    return 'CommerceMigrateUbercartProduct' . ucfirst($this->arguments['type']);
  }
}
