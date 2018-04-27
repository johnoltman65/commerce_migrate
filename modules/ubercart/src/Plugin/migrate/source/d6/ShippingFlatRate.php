<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\d6;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Gets the flat rate shipping service.
 *
 * @MigrateSource(
 *   id = "d6_ubercart_shipping_flat_rate",
 *   source_module = "uc_flatrate"
 * )
 */
class ShippingFlatRate extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('uc_flatrate_methods', 'f')->fields('f');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'mid' => t('Method id'),
      'title' => t('Flat rate title'),
      'label' => t('Flat rate label'),
      'base_rate' => t('Base rate'),
      'product_rate' => t('Product rate'),
      'number' => t('The amount converted to a Commerce price amount'),
      'currency_code' => t('Currency code'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $currency_code = $this->variableGet('currency_code', 'USD');
    $row->setSourceProperty('currency_code', $currency_code);
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'mid' => [
        'type' => 'string',
      ],
    ];
  }

}
