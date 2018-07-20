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
    $query->leftJoin('uc_countries', 'uc', 'uc.country_id = uo.billing_country');
    $query->addField('uc', 'country_iso_code_2');
    $query->leftJoin('uc_zones', 'uz', 'uz.zone_id = uo.billing_zone');
    $query->addField('uz', 'zone_code');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'order_id' => $this->t('Order ID'),
      'uid' => $this->t('User ID of order'),
      'order_status' => $this->t('Order status'),
      'order_total' => $this->t('Order total'),
      'product_count' => $this->t('Product count'),
      'primary_email' => $this->t('Email associated with order'),
      'delivery_first_name' => $this->t('Delivery first name'),
      'delivery_last_name' => $this->t('Delivery last name'),
      'delivery_phone' => $this->t('Delivery phone name'),
      'delivery_company' => $this->t('Delivery company name'),
      'delivery_street1' => $this->t('Delivery street address line 1'),
      'delivery_street2' => $this->t('Delivery street address line 2'),
      'delivery_city' => $this->t('Delivery city'),
      'delivery_zone' => $this->t('Delivery State'),
      'delivery_postal_code' => $this->t('delivery postal code'),
      'delivery_country' => $this->t('delivery country'),
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
      'payment_method' => $this->t('Payment method'),
      'data' => $this->t('Order attributes'),
      'created' => $this->t('Date/time of order creation'),
      'modified' => $this->t('Date/time of last order modification'),
      'host' => $this->t('IP address of customer'),
      'currency' => $this->t('Currency'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $row->setSourceProperty('data', unserialize($row->getSourceProperty('data')));
    return parent::prepareRow($row);
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

}
