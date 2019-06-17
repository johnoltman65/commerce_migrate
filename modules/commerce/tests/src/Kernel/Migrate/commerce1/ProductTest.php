<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\commerce1;

use Drupal\Core\Database\Database;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;
use Drupal\Tests\migrate\Kernel\MigrateDumpAlterInterface;

/**
 * Tests product migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce1
 */
class ProductTest extends Commerce1TestBase implements MigrateDumpAlterInterface {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'commerce_price',
    'commerce_product',
    'commerce_store',
    'path',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->migrateProducts();
  }

  /**
   * {@inheritdoc}
   */
  public static function migrateDumpAlter(KernelTestBase $test) {
    $db = Database::getConnection('default', 'migrate');

    // Remove all product types that can be referenced by the hat content type.
    $results = $db->select('field_config_instance', 'fci')
      ->condition('field_name', 'field_product')
      ->condition('entity_type', 'node')
      ->condition('bundle', 'hats')
      ->fields('fci', ['data'])
      ->execute()
      ->fetchCol();

    if ($results) {
      $data = unserialize(reset($results));
    }
    $data['settings']['referenceable_types'] = [
      'bags_cases' => 0,
      'drinks' => 0,
      'hats' => 0,
      'shoes' => 0,
      'storage_devices' => 0,
      'tops' => 0,
    ];

    $data = serialize($data);
    $db->update('field_config_instance')
      ->condition('field_name', 'field_product')
      ->condition('entity_type', 'node')
      ->condition('bundle', 'hats')
      ->fields(['data' => $data])
      ->execute();

    // Change the  product types that can be referenced by the shoe content
    // type to 'hats' and 'shoes'.
    $results = $db->select('field_config_instance', 'fci')
      ->condition('field_name', 'field_product')
      ->condition('entity_type', 'node')
      ->condition('bundle', 'shoes')
      ->fields('fci', ['data'])
      ->execute()
      ->fetchCol();

    if ($results) {
      $data = unserialize(reset($results));
    }
    $data['settings']['referenceable_types'] = [
      'bags_cases' => 0,
      'drinks' => 0,
      'hats' => 'shoes',
      'shoes' => 'shoes',
      'storage_devices' => 0,
      'tops' => 0,
    ];

    $data = serialize($data);
    $db->update('field_config_instance')
      ->condition('field_name', 'field_product')
      ->condition('entity_type', 'node')
      ->condition('bundle', 'shoes')
      ->fields(['data' => $data])
      ->execute();
  }

  /**
   * Test product migration from Drupal 7 to 8.
   */
  public function testProduct() {
    $this->assertProductEntity(
      15,
      'bags_cases',
      '1',
      'Go green with Drupal Commerce Reusable Tote Bag',
      TRUE,
      ['1'],
      ['1']
    );
    $this->assertProductEntity(
      22,
      'hats',
      '1',
      'Commerce Guys Baseball Cap',
      TRUE,
      ['1'],
      ['1']
    );
    $this->assertProductEntity(
      23,
      'hats',
      '1',
      'Drupal Commerce Ski Cap',
      TRUE,
      ['1'],
      ['1']
    );
    // Tests a product with multiple variations.
    $this->assertProductEntity(
      26,
      'storage_devices',
      '1',
      'Commerce Guys USB Key',
      TRUE,
      ['1'],
      [
        '28',
        '29',
        '30',
      ]
    );
  }

}
