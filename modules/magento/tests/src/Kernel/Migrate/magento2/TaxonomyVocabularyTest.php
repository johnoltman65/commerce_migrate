<?php

namespace Drupal\Tests\commerce_migrate_magento\Kernel\Migrate\magento2;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;
use Drupal\Tests\commerce_migrate_magento\Kernel\Migrate\CsvTestBase;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\VocabularyInterface;

/**
 * Migrate category.
 *
 * @requires module migrate_source_csv
 *
 * @group commerce_migrate
 * @group commerce_migrate_magento2
 */
class TaxonomyVocabularyTest extends CsvTestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'taxonomy',
    'commerce_migrate',
    'commerce_migrate_magento',
  ];

  /**
   * File path of the test fixture.
   *
   * @var string
   */
  protected $fixture = 'public://import/magento2-catalog_product_20180326_013553.csv';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->executeMigration('magento2_category');
  }

  /**
   * Tests Magento 2 category to Drupal 8 vocabulary migration.
   */
  public function testTaxonomyVocabulary() {
    $this->assertEntity('default_category', 'Default Category', NULL, VocabularyInterface::HIERARCHY_DISABLED, 0);
  }

  /**
   * Validate a migrated vocabulary contains the expected values.
   *
   * @param string $id
   *   Entity ID to load and check.
   * @param string $expected_label
   *   The label the migrated entity should have.
   * @param string $expected_description
   *   The description the migrated entity should have.
   * @param string $expected_hierarchy
   *   The hierarchy setting the migrated entity should have.
   * @param string $expected_weight
   *   The weight the migrated entity should have.
   */
  protected function assertEntity($id, $expected_label, $expected_description, $expected_hierarchy, $expected_weight) {
    /** @var \Drupal\taxonomy\VocabularyInterface $entity */
    $entity = Vocabulary::load($id);
    $this->assertTrue($entity instanceof VocabularyInterface);
    $this->assertSame($expected_label, $entity->label());
    $this->assertSame($expected_description, $entity->getDescription());
    $this->assertSame($expected_hierarchy, $entity->getHierarchy());
    $this->assertSame($expected_weight, $entity->get('weight'));
  }

}
