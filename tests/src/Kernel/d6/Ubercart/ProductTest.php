<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\commerce_product\Entity\Product;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests Product migration.
 *
 * @group commerce_migrate
 */
class ProductTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'path',
    'commerce_product',
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
      'ubercart_currency',
      'd6_ubercart_product_variation',
      'd6_ubercart_product',
    ]);
  }

  /**
   * Test product migration from Drupal 6 to 8.
   */
  public function testProduct() {
    $this->assertProductEntity(1, 'Bath Towel', TRUE, '1492989524', ['1']);
    /** @var \Drupal\commerce_product\Entity\ProductVariation $variation */
    $variation = Product::load(1)->variations->first()->entity;
    $this->assertSame('1', $variation->id());
    $this->assertProductVariationEntity(1, 'towel-bath-001', '20.000000', 'NZD');

    $this->assertProductEntity(3, 'Fairy cake', TRUE, '1492989703', ['1']);
  }

}

