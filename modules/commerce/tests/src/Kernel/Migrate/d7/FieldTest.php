<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\FieldStorageConfigInterface;

/**
 * Tests commerce field migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class FieldTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'comment',
    'datetime',
    'file',
    'image',
    'link',
    'node',
    'system',
    'taxonomy',
    'telephone',
    'text',
    'path',
    'commerce_product',
    'migrate_plus',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(static::$modules);
    $this->executeMigrations([
      'd7_commerce_product_variation_type',
      'd7_commerce_product_type',
      'd7_field',
    ]);
  }

  /**
   * Test field migration from Drupal 7 to Drupal 8.
   */
  public function testField() {
    /** @var \Drupal\field\FieldStorageConfigInterface $field */
    $field = FieldStorageConfig::load('comment.comment_body');
    $this->assertInstanceOf(FieldStorageConfigInterface::class, $field);

    // Commerce product variation field storage.
    $field = FieldStorageConfig::load('commerce_product_variation.field_bag_size');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product_variation.field_color');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product_variation.field_hat_size');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product_variation.field_images');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product_variation.field_shoe_size');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product_variation.field_storage_capacity');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product_variation.title_field');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product_variation.field_top_size');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product_variation.title_field');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    // The default price on product in D8 is a base field without a field
    // storage so migrating this could be skipped. However, the source product
    // may have additional price field so migrate them all.
    // @TODO find a way to not migrate the base price field storage.
    $field = FieldStorageConfig::load('commerce_product_variation.commerce_price');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);

    // Commerce product field storage.
    $field = FieldStorageConfig::load('commerce_product.body');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_brand');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_category');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_collection');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_gender');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_product');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.title_field');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);

    // Commerce product field storage should not be duplicated on nodes.
    $field = FieldStorageConfig::load('node.field_brand');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.field_category');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.field_collection');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.field_gender');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.field_product');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);

    // Node field storage.
    $field = FieldStorageConfig::load('node.field_blog_category');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.body');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.title_field');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.field_headline');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.field_image');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.field_link');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.field_tags');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.field_tagline');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('node.title_field');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);

    // Node only field storage should not be duplicated on commerce products.
    $field = FieldStorageConfig::load('commerce_product.field_blog_category');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_headline');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_image');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_link');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_tags');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('commerce_product.field_tagline');
    $this->assertFalse($field instanceof FieldStorageConfigInterface);

    $field = FieldStorageConfig::load('taxonomy_term.field_image');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);
    $field = FieldStorageConfig::load('taxonomy_term.field_category_color');
    $this->assertTrue($field instanceof FieldStorageConfigInterface);

    // Test that a rerun of the migration does not cause errors.
    $this->executeMigration('d7_field');
    $migration = $this->getMigration('d7_field');
    $errors = $migration->getIdMap()->errorCount();
    $this->assertSame(0, $errors);
  }

}
