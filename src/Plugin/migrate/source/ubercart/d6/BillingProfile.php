<?php

namespace Drupal\commerce_migrate\Plugin\migrate\source\ubercart\d6;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "d6_ubercart_billing_profile"
 * )
 */
class BillingProfile extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {

    $order_ids = $this->getOrderIds();

    $query = $this->select('uc_orders', 'uo')->fields('uo', [
      'order_id', 'uid', 'billing_first_name', 'billing_last_name',
      'billing_company', 'billing_street1', 'billing_street2', 'billing_city',
      'billing_zone', 'billing_postal_code', 'billing_country', 'created',
      'modified',
    ]);
    $query->condition('order_id', $order_ids, 'IN');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'order_id' => $this->t('Unique Order ID'),
      'uid' => $this->t('Unique User ID'),
      'billing_first_name' => $this->t('Driver first name'),
      'billing_last_name' => $this->t('Driver last name'),
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
   * Queries database for the order ids of the most recently modified billing
   * addresses. It assumes the billing address on the most recent order are
   * the most current.
   */
  public function getOrderIds() {
    $query = $this->select('uc_orders', 'uo')
      ->fields('uo', ['order_id']);
    $query->addExpression('MAX(uo.modified)', 'newest_entry');
    $query->groupBy('uo.uid');
    $query->groupBy('uo.order_id');

    $order_ids = [];
    foreach ($query->execute()->fetchAll() as $row) {
      $order_ids[] = $row['order_id'];
    }

    return $order_ids;
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
