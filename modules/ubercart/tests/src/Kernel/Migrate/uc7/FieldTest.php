<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc7;

use Drupal\field\Entity\FieldStorageConfig;

/**
 * Migrate Ubercart 7 field tests.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc7
 */
class FieldTest extends Ubercart7TestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'comment',
    'commerce_price',
    'commerce_product',
    'commerce_store',
    'datetime',
    'image',
    'link',
    'migrate_plus',
    'node',
    'path',
    'taxonomy',
    'telephone',
    'text',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(static::$modules);
    $this->installEntitySchema('action');
    $this->installEntitySchema('commerce_product');
    $this->executeMigration('d7_field');
  }

  /**
   * Tests the Ubercart 7 field to Drupal 8 migration.
   */
  public function testFields() {
    /** @var \Drupal\field\Entity\FieldStorageConfig $field_storage */
    $field_storage = FieldStorageConfig::load('commerce_product.field_number');
    $this->assertInstanceOf(FieldStorageConfig::class, $field_storage);
    $field_storage = FieldStorageConfig::load('commerce_product.field_sustainability');
    $this->assertInstanceOf(FieldStorageConfig::class, $field_storage);
    $field_storage = FieldStorageConfig::load('commerce_product.taxonomy_catalog');
    $this->assertInstanceOf(FieldStorageConfig::class, $field_storage);
    $field_storage = FieldStorageConfig::load('commerce_product.uc_product_image');
    $this->assertInstanceOf(FieldStorageConfig::class, $field_storage);

    $field_storage = FieldStorageConfig::load('node.field_image');
    $this->assertInstanceOf(FieldStorageConfig::class, $field_storage);
    $field_storage = FieldStorageConfig::load('node.field_number');
    $this->assertInstanceOf(FieldStorageConfig::class, $field_storage);
    $field_storage = FieldStorageConfig::load('node.field_sustainability');
    $this->assertNull($field_storage);
    $field_storage = FieldStorageConfig::load('node.uc_product_image');
    $this->assertNull($field_storage);
  }

}
