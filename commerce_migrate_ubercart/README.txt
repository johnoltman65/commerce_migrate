Commerce Migrate Ubercart README
--------------------------------

This module is a demonstration of how to import an Ubercart site into Drupal
Commerce. It takes into account many of the standard features of Ubercart
and of Drupal Commerce, but since real installations can vary quite widely,
it would be unreasonable to expect this stock migration technique to bring all
of your data over from an arbitrary Ubercart site.


Assumptions and Limitations:
----------------------------
* This assumes that we're importing from and to the same site. In other words,
  all the existing Ubercart data is in the same database with the Commerce data.
  This is not optimal, and hopefully we'll do something better at some point.
  See http://drupal.org/node/1206348.
* The product image field is assumed to be field_data_uc_product_image on the
  ubercart site, and field_image (on every product) on the Commerce side.
* Product types are created *by the migration* from the base "Product" type
  and the uc_product_classes table.
* Delete any existing products, line items, or orders. Delete existing product
  types.
* Consider deleting existing product display nodes.
* See http://drupal.org/node/1206776#comment-4685032 for one import recipe.  