<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;


/**
 * Tests order item type migration.
 *
 * @group migrate_drupal_7
 */
class MigrateOrderItemTypeTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->executeMigration('d7_commerce_order_item_type');
  }

  /**
   * Tests the Drupal 6 taxonomy vocabularies to Drupal 8 migration.
   */
  public function testOrderItemType() {
    $this->assertOrderItemType('product', "product");
  }

}
