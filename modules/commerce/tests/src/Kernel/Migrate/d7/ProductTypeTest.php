<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests line item migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class ProductTypeTest extends Commerce1TestBase {

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
    // @todo Execute the d7_field and d7_field_instance migrations?
    $migration = $this->getMigration('d7_commerce_product_type');
    $this->executeMigration($migration);

    // Rerun the migration.
    $table_name = $migration->getIdMap()->mapTableName();
    $default_connection = \Drupal::database();
    $default_connection->truncate($table_name)->execute();
    $this->executeMigration($migration);
  }

  /**
   * Test product type migration from Drupal 7 to 8.
   */
  public function testProductType() {
    $type = [
      'id' => 'bags_cases',
      'label' => 'Bags & Cases',
      'description' => 'A <em>Bags & Cases</em> is a content type which contain product variations.',
      'variation_type' => 'bags_cases',
    ];
    $this->assertProductTypeEntity($type['id'], $type['label'], $type['description'], $type['variation_type']);
    $type = [
      'id' => 'tops',
      'label' => 'Tops',
      'description' => 'A <em>Tops</em> is a content type which contain product variations.',
      'variation_type' => 'tops',
    ];
    $this->assertProductTypeEntity($type['id'], $type['label'], $type['description'], $type['variation_type']);
  }


}
