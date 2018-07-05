<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\uc6;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Gets the Ubercart tax rates.
 *
 * @MigrateSource(
 *   id = "uc6_tax_type",
 *   source_module = "uc_taxes"
 * )
 */
class TaxType extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('uc_taxes', 'ut')
      ->fields('ut', ['id', 'name', 'rate']);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('TaxType ID'),
      'name' => $this->t('TaxType Name'),
      'rate' => $this->t('TaxType Rate'),
      'country_iso_code_2' => $this->t('Country 2 character code'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $country = $this->variableGet('uc_store_country', NULL);
    // Get the country iso code 2 for this country.
    $query = $this->select('uc_countries', 'ucc')
      ->fields('ucc', ['country_iso_code_2'])
      ->condition('country_id', $country);
    $country_iso_code_2 = $query->execute()->fetchField();
    $row->setSourceProperty('country_iso_code_2', $country_iso_code_2);
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type' => 'integer',
        'alias' => 'ut',
      ],
    ];
  }

}
