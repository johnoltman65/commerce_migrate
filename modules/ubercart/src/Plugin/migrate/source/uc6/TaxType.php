<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\uc6;

use Drupal\commerce_migrate_ubercart\Plugin\migrate\source\TaxTypeBase;

@trigger_error('TaxType is deprecated in Commerce Migrate 8.x-2.0-beta4 and will be removed before Commerce Migrate 8.x-3.x. Use \Drupal\commerce_migrate_ubercart\Plugin\migrate\source\TaxTypeBase instead. See https://www.drupal.org/node/3000782 for more information.', E_USER_DEPRECATED);

/**
 * Gets the Ubercart tax rates.
 *
 * @MigrateSource(
 *   id = "uc6_tax_type",
 *   source_module = "uc_taxes"
 * )
 * @deprecated in Commerce Migrate 8.x-2.0-beta4, to be removed before Commerce
 * Migrate 8.x-3.x. Use
 * Drupal\commerce_migrate_ubercart\Plugin\migrate\source\TaxTypeBase instead.
 * See https://www.drupal.org/node/3000782 for more information.
 */
class TaxType extends TaxTypeBase {}
