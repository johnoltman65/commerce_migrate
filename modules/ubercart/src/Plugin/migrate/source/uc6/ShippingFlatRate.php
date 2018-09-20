<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\source\uc6;

use Drupal\commerce_migrate_ubercart\Plugin\migrate\source\ShippingFlatRate as UbercartShippingFlatRatet;

@trigger_error('ShippingFlatRate is deprecated in Commerce Migrate 8.x-2.x-beta4 and will be removed before Commerce Migrate 8.x-3.x. Use \Drupal\commerce_migrate\modules\ubercart\source\ShippingFlatRate instead. See https://www.drupal.org/node/3000816 for more information.', E_USER_DEPRECATED);
/**
 * Gets the flat rate shipping service.
 *
 * @MigrateSource(
 *   id = "uc6_shipping_flat_rate",
 *   source_module = "uc_flatrate"
 * )
 *
 * @deprecated in Commerce Migrate 8.x-2.x-beta4, to be removed before
 * Commerce Migrate 8.x-3.x. Use
 * \Drupal\commerce_migrate\modules\ubercart\source\OrderProduct instead. See
 * https://www.drupal.org/node/3000816 for more information.
 */
class ShippingFlatRate extends UbercartShippingFlatRatet {}
