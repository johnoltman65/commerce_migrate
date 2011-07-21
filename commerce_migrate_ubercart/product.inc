<?php

/**
 * @file
 *   Commerce Product migration.
 *   This is a dynamic migration, reused for every product type
 *   (so that products of each type can be imported separately)
 *   @todo Migrate the product image.
 */

class CommerceMigrateUbercartProductMigration extends DynamicMigration {

  protected $file_public_path = "";
  protected $file_private_path = "";
  protected $file_temporary_path = "";

  public function __construct(array $arguments) {
    $this->arguments = $arguments;
    parent::__construct();
    $this->description = t('Import products from Ubercart.');
    $this->dependencies = array('CommerceMigrateUbercartProductType');

    // Find out where the public and private files are stored in the remote db
    // so that we can find the files and process them.
//    $connection = commerce_migrate_ubercart_get_source_connection();
//    $query = $connection->select('variable', 'v')
//      ->fields('v', array('name', 'value'))
//      ->condition('v.name', 'file_%_path', 'like');
//    $result = $query->execute();
//    foreach ($result as $record) {
//      $this->{$record->name} = unserialize($record->value);
//    }
    $this->file_public_path = commerce_migrate_ubercart_get_public_filepath();

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
    $connection = commerce_migrate_ubercart_get_source_connection();
    $query = $connection->select('node', 'n');
    $query->innerJoin('uc_products', 'ucp', 'n.nid = ucp.nid AND n.vid = ucp.vid');
    $query->fields('n', array('nid', 'vid', 'type', 'title', 'created', 'changed'))
          ->fields('ucp', array('model', 'sell_price'))
          ->condition('n.type', $arguments['type'])
          ->distinct();
    $this->source = new MigrateSourceSQL($query, array(), NULL, array('map_joinable' => FALSE));

//    $this->source = new MigrateSourceSQL($query);
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
      ->arguments(array('file_function' => 'file_copy'));
  }

  public function prepareRow($row) {
    // If the dollar currency is disabled, enable it.
    $enabled_currencies = variable_get('commerce_enabled_currencies', array('USD' => 'USD'));
    if (empty($enabled_currencies['USD'])) {
      $enabled_currencies['USD'] = 'USD';
      variable_set('commerce_enabled_currencies', $enabled_currencies);
    }

    // Prepare the imagefields, from http://drupal.org/node/1159234
    $connection = commerce_migrate_ubercart_get_source_connection();

    switch (SOURCE_DATABASE_DRUPAL_VERSION) {
      case 'd7':
        $query = $connection->select('field_data_uc_product_image', 'img');
        $query->innerJoin('file_managed', 'f', 'img.uc_product_image_fid = f.fid');
        $query->fields('f', array('fid', 'uri'));
        $query->fields('img', array('uc_product_image_alt', 'uc_product_image_title'));
        $query->condition('img.entity_id', $row->nid)
          ->condition('img.revision_id', $row->vid);
        break;
      case 'd6':
        $query = $connection->select('content_field_image_cache', 'img');
        $query->innerJoin('files', 'f', 'field_image_cache_fid = f.fid');
        $query->addField('f', 'filepath', 'uri'); // Pretend it's D7 uri.
        $query->addField('f', 'fid', 'fid');
        $query->addField('img', 'field_image_cache_data');
        $query->condition('img.nid', $row->nid)
          ->condition('img.vid', $row->vid);
        break;
    }

    $result = $query->execute();
    foreach ($result as $image_row) {
      $path = $image_row->uri;
      $path = str_replace('public://', $this->file_public_path . '/', $path);
      switch (SOURCE_DATABASE_DRUPAL_VERSION) {
        case 'd7':
          $value = array(
            'path' => $path,
            'alt' => $image_row->uc_product_image_alt,
            'title' => $image_row->uc_product_image_title,
          );
          break;
        case 'd6':
          $value = drupal_json_decode($image_row->field_image_cache_data);
          break;
      }
      $row->images[] = drupal_json_encode($value);
    }
  }

  /**
   * Construct the machine name (identifying the migration in "drush ms" and other places).
   */
  protected function generateMachineName($class_name = NULL) {
    return 'CommerceMigrateUbercartProduct' . ucfirst($this->arguments['type']);
  }
}
