<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\uc6;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Ubercart 6 billing profile source.
 *
 * @MigrateSource(
 *   id = "uc6_billing_profile",
 *   source_module = "uc_order"
 * )
 */
class BillingProfile extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Gets billing information for the single most recent order for each
    // customer. This assumes the billing information on the most recent order
    // is the most current.
    $query = $this->select('uc_orders', 'uo')->fields('uo');
    $query->leftJoin('uc_orders', 'uo2', 'uo.uid = uo2.uid AND uo.modified < uo2.modified');
    $query->isNull('uo2.order_id');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'order_id' => $this->t('Unique Order ID'),
      'uid' => $this->t('Unique User ID'),
      'billing_first_name' => $this->t('Billing first name'),
      'billing_last_name' => $this->t('Billing last name'),
      'billing_phone' => $this->t('Billing phone name'),
      'billing_company' => $this->t('Billing company name'),
      'billing_street1' => $this->t('Billing street address line 1'),
      'billing_street2' => $this->t('Billing street address line 2'),
      'billing_city' => $this->t('Billing city'),
      'billing_zone' => $this->t('Billing State'),
      'billing_postal_code' => $this->t('Billing postal code'),
      'billing_country' => $this->t('Billing country'),
      'created' => $this->t('Created'),
      'modified' => $this->t('Modified'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'order_id' => [
        'type' => 'integer',
        'alias' => 'uo',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // In the Ubercart 6 order table, countries are stored by country ID
    // integer value but in Commerce 8, they are stored as ISO codes. This
    // query uses the Ubercart 6 'uc_countries' table as a lookup.
    $country_code = $this->select('uc_countries', 'uc')
      ->fields('uc', ['country_iso_code_2'])
      ->condition('country_id', $row->getSourceProperty('billing_country'))
      ->execute()
      ->fetchCol();
    $row->setSourceProperty('billing_country', $country_code[0]);

    // In the Ubercart 6 order table, zones (state, provinces, etc.) are
    // stored as foreign key values so looking up the zone abbreviations
    // in the 'uc_zones' table is necessary.
    $administrative_area = $this->select('uc_zones', 'uz')
      ->fields('uz', ['zone_code'])
      ->condition('zone_id', $row->getSourceProperty('billing_zone'))
      ->execute()
      ->fetchCol();

    if (!empty($administrative_area[0])) {
      $row->setSourceProperty('billing_zone', $country_code[0] . '-' . $administrative_area[0]);
    }
    else {
      $row->setSourceProperty('billing_zone', '');
    }

    return parent::prepareRow($row);
  }

}
