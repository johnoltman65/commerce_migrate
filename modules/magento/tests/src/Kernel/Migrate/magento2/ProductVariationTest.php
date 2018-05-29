<?php

namespace Drupal\Tests\commerce_migrate_magento\Kernel\Migrate\magento2;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;
use Drupal\Tests\commerce_migrate_magento\Kernel\Migrate\CsvTestBase;

/**
 * Tests Product migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_magento2
 */
class ProductVariationTest extends CsvTestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'action',
    'address',
    'entity',
    'field',
    'inline_entity_form',
    'options',
    'path',
    'system',
    'text',
    'user',
    'views',
    'commerce',
    'commerce_price',
    'commerce_store',
    'commerce',
    'commerce_product',
    'commerce_migrate',
    'commerce_migrate_magento',
  ];

  /**
   * Filename of the test fixture.
   *
   * @var string
   */
  protected $fixture = 'public://import/magento2-catalog_product_20180326_013553_test.csv';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installSchema('user', ['users_data']);
    // Make sure uid 1 is created.
    user_install();

    $this->installConfig('commerce_product');
    $this->installEntitySchema('commerce_store');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_product');
    $this->executeMigration('magento2_product_variation');
  }

  /**
   * Test product variation migration.
   */
  public function testProductVariation() {
    $this->assertProductVariationEntity(1, '1', '24-MB01', '34.000000', 'USD', NULL, '', 'default', '1521962400', '1521962400');
    $this->assertProductVariationEntity(2, '1', '24-MB02', '59.000000', 'USD', NULL, '', 'default', '1521962400', '1521962400');
    $this->assertProductVariationEntity(3, '1', '24-UB02', '74.000000', 'USD', NULL, '', 'default', '1521962400', '1521962400');
    $this->assertProductVariationEntity(4, '1', '24-WB01', '32.000000', 'USD', NULL, '', 'default', '1521962400', '1521962400');
    $this->assertProductVariationEntity(5, '1', '24-WB02', '32.000000', 'USD', NULL, '', 'default', '1521962400', '1521962400');
    // Test a product with a fractional price.
    $this->assertProductVariationEntity(31, '1', 'MSH02-32-Black', '32.500000', 'USD', NULL, '', 'default', '1521962520', '1521962520');

  }

}
