<?php

/**
 * @file
 *   Commerce Order migration.
 */

class CommerceMigrateUbercartOrderMigration extends Migration {
  public function __construct() {
    parent::__construct();
    $this->description = t('Import orders from Ubercart.');
    $this->dependencies = array('CommerceMigrateUbercartCustomerBillingProfile');

    // Create a map object for tracking the relationships between source rows
    $this->map = new MigrateSQLMap($this->machineName,
        array(
          'order_id' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'description' => 'Ubercart order ID',
          ),
        ),
        MigrateDestinationEntityAPI::getKeySchema('commerce_order', 'commerce_order')
      );

    // Create a MigrateSource object, which manages retrieving the input data.
    $query = db_select('uc_orders', 'uo')
             ->fields('uo', array('order_id', 'uid', 'order_status', 'order_total', 
                                  'primary_email', 'created', 'modified', 'host'));
    $this->source = new MigrateSourceSQL($query);
    $this->destination = new MigrateDestinationEntityAPI('commerce_order', 'commerce_order');

    // Properties
    $this->addFieldMapping('order_number', 'order_id');
    $this->addFieldMapping('mail', 'primary_email');
    $this->addFieldMapping('hostname', 'host');
    $this->addFieldMapping('status', 'order_status');
    $this->addFieldMapping('uid', 'uid');
    $this->addFieldMapping('created', 'created');
    $this->addFieldMapping('changed', 'modified');
    // Fields
    $this->addFieldMapping('commerce_customer_billing', 'order_id')
         ->sourceMigration('CommerceMigrateUbercartCustomerBillingProfile');
    $this->addFieldMapping('commerce_order_total', 'order_total');
  }
  
  function prepare($order, stdClass $row) {
    // Most order statuses (pending, cancelled, completed) are the same
    // between the two systems. However, some are not.
    if ($order->status == 'in_checkout') {
      $order->status = 'checkout_checkout';
    }
    elseif ($order->status == 'payment_received') {
      // @todo Revisit this, these two statuses are not really the same.
      $order->status = 'checkout_payment';
    }
  }
  
  /**
   * The line item controller kills the order_total we set.
   * Until that gets fixed, here's a workaround.
   */
  function complete($order, stdClass $row) {
    db_update('field_data_commerce_order_total')
      ->fields(array(
        'commerce_order_total_amount' => commerce_currency_decimal_to_amount($row->order_total, 'USD'),
      ))
      ->condition('entity_id', $order->order_id)
      ->condition('entity_type', 'commerce_order')
      ->execute();
  }
}
