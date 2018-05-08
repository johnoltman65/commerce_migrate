<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\uc6;

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
    ];
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
