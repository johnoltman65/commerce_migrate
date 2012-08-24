Provides:
- An Entity API migrate destination plugin.
- Migrate destination field handlers for commerce fields (reference fields, price field)
- Migrate destination plugin for commerce products.
- Migrate destination plugin for commerce product types.

Ubercart migration
------------------
Commerce Migrate Ubercart has moved to its own project, 
http://drupal.org/project/commerce_migrrate_ubercart

Migrate
-------
Migrate (http://drupal.org/project/migrate) is one of the two solutions for importing external data into Drupal,
the other being Feeds (see http://drupal.org/project/feeds and http://drupal.org/project/commerce_feeds)

Please see the project page for more information.

Price fields
------------

Tax rates and currencies can be migrated either as arguments or subfields.

As an argument, these are set once in the mapping:

<?php
$arguments = MigrateCommercePriceFieldHandler::arguments('your_currency_code', 'some_vat_name'));
$this->addFieldMapping('commerce_price', 'price')
  ->arguments($arguments);
?>

As subfields, these can more easily be different for each record of the
migration:

<?php
$this->addFieldMapping('commerce_price', 'price');
$this->addFieldMapping('commerce_price:currency_code', NULL)
  ->defaultValue('GBP');
$this->addFieldMapping('commerce_price:tax_rate', 'price_tax')
  ->description(t('The tax rate is in the price_tax field in the source.'));
?>

