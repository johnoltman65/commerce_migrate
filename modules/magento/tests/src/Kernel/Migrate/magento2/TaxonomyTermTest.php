<?php

namespace Drupal\Tests\commerce_migrate_magento\Kernel\Migrate\magento2;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;
use Drupal\Tests\commerce_migrate_magento\Kernel\Migrate\CsvTestBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermInterface;

/**
 * Migrate category.
 *
 * @requires module migrate_source_csv
 *
 * @group commerce_migrate
 * @group commerce_migrate_magento2
 */
class TaxonomyTermTest extends CsvTestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'taxonomy',
    'text',
    'commerce_migrate',
    'commerce_migrate_magento',
  ];

  /**
   * The cached taxonomy tree items, keyed by vid and tid.
   *
   * @var array
   */
  protected $treeData = [];

  /**
   * Filename of the test fixture.
   *
   * @var string
   */
  protected $fixture = 'public://import/magento2-catalog_product_20180326_013553.csv';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('taxonomy_term');
    $this->executeMigration('magento2_category');
    $this->executeMigration('magento2_category_term');
  }

  /**
   * Validate a migrated term contains the expected values.
   *
   * @param string $id
   *   Entity ID to load and check.
   * @param string $expected_label
   *   The label the migrated entity should have.
   * @param string $expected_vid
   *   The parent vocabulary the migrated entity should have.
   * @param string $expected_description
   *   The description the migrated entity should have.
   * @param string $expected_format
   *   The format the migrated entity should have.
   * @param int $expected_weight
   *   The weight the migrated entity should have.
   * @param array $expected_parents
   *   The parent terms the migrated entity should have.
   */
  protected function assertEntity($id, $expected_label, $expected_vid, $expected_description = '', $expected_format = NULL, $expected_weight = 0, array $expected_parents = []) {
    /** @var \Drupal\taxonomy\TermInterface $entity */
    $entity = Term::load($id);
    $this->assertInstanceOf(TermInterface::class, $entity);
    $this->assertEquals($expected_label, $entity->label());
    $this->assertEquals($expected_vid, $entity->bundle());
    $this->assertEquals($expected_description, $entity->getDescription());
    $this->assertEquals($expected_format, $entity->getFormat());
    $this->assertEquals($expected_weight, $entity->getWeight());
    $parent_ids = array_keys(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($id));
    $this->assertEquals($expected_parents, $parent_ids);
    $this->assertHierarchy($expected_vid, $id, $expected_parents);
  }

  /**
   * Tests the Drupal 7 taxonomy term to Drupal 8 migration.
   */
  public function testTaxonomyTerm() {
    $this->assertEntity(1, 'Gear', 'default_category', '', NULL, 0, []);
    $this->assertEntity(2, 'Bags', 'default_category', '', NULL, 0, [1]);
    $this->assertEntity(3, 'Collections', 'default_category', '', NULL, 0, []);
    $this->assertEntity(4, 'New Luma Yoga Collection', 'default_category', '', NULL, 0, [3]);
    $this->assertEntity(8, 'Video Download', 'default_category', '', NULL, 0, [7]);
  }

  /**
   * Assert that a term is present in the tree storage, with the right parents.
   *
   * @param string $vid
   *   Vocabular ID.
   * @param int $tid
   *   ID of the term to check.
   * @param array $parent_ids
   *   The expected parent term IDs.
   */
  protected function assertHierarchy($vid, $tid, array $parent_ids) {
    if (!isset($this->treeData[$vid])) {
      $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
      $this->treeData[$vid] = [];
      foreach ($tree as $item) {
        $this->treeData[$vid][$item->tid] = $item;
      }
    }

    $this->assertArrayHasKey($tid, $this->treeData[$vid], "Term $tid exists in taxonomy tree");
    $term = $this->treeData[$vid][$tid];
    $this->assertEquals($parent_ids, array_filter($term->parents), "Term $tid has correct parents in taxonomy tree");
  }

}
