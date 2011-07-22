<?php

/**
 * @file
 *   Commerce Customer Profile Billing migration.
 *   Ubercart stores this info with the order. We create one new
 *   profile per order.
 */

class CommerceMigrateUbercartCustomerBillingProfileMigration extends Migration {
  public function __construct() {
    parent::__construct();
    $this->description = t('Import customer billing profiles from Ubercart.');

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
        MigrateDestinationEntityAPI::getKeySchema('commerce_customer_profile', 'billing')
      );

    // Create a MigrateSource object, which manages retrieving the input data.
    $connection = commerce_migrate_ubercart_get_source_connection();

    $query = $connection->select('uc_orders', 'uo');
    $query->leftJoin('uc_countries', 'uc', 'uo.billing_country = uc.country_id');
    $query->leftJoin('uc_zones', 'uz', 'uo.billing_zone = uz.zone_id');
    $query->fields('uo', array('order_id', 'billing_first_name', 'billing_last_name',
                                'billing_company', 'billing_street1',
                                'billing_city', 'billing_postal_code',
                                'created'));
    $query->fields('uc', array('country_iso_code_2'));
    $query->fields('uz', array('zone_code'));
    $query->condition('uo.order_status', 'in_checkout', '<>');
    if (variable_get('commerce_migrate_ubercart_user_map_ok', FALSE)) {
      $query->addField('uo', 'uid', 'uid');
    }

    $this->source = new MigrateSourceSQL($query, array(), NULL, array('map_joinable' => FALSE));
    $this->destination = new MigrateDestinationEntityAPI('commerce_customer_profile', 'billing');

    // Properties
    // Default uid to 0 if we're not mapping it.
    $this->addFieldMapping('uid', 'uid')->defaultValue(0);
    $this->addFieldMapping('created', 'created');
    $this->addFieldMapping('changed', 'created');
  }

  public function prepare($profile, stdClass $row) {
    $profile->type = 'billing';
    $profile->status = TRUE;

    $name = $row->billing_first_name . ' ' . $row->billing_last_name;
    // There are too many fields to do this through a field handler.
    // @todo Not comfortable with assuming LANGUAGE_NONE.
    $profile->commerce_customer_address[LANGUAGE_NONE][0] = array(
        'xnl' => '<NameDetails PartyType="Person"><NameLine>' . $name . '</NameLine></NameDetails>',
        'name_line' => $name,
        'administrative_area' => $row->zone_code,
        'country' => $row->country_iso_code_2,
        'thoroughfare' => $row->billing_street1,
        'locality' => $row->billing_city,
        'postal_code' => $row->billing_postal_code,
        'organisation_name' => $row->billing_company,
      );
  }
}
