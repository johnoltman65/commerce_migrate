<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests product type migration.
 *
 * @group commerce_migrate
 */
class MigrateProductTypeTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'path',
    'commerce_product',
    'commerce_migrate_ubercart',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('view');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_product');
    $this->installConfig(['commerce_product']);
    $this->migrateStore();
    $this->executeMigrations([
      'd6_ubercart_product_variation',
      'd6_ubercart_product',
      'd6_ubercart_product_type',
    ]);
  }

  /**
   * Test product type migration from Drupal 6 to 8.
   */
  public function testProductType() {
    $description = 'A type of spacecraft capable of traveling to the solar systems of other stars';
    $this->assertProductTypeEntity('ship', 'Ship', $description, 'default');
  }

}
