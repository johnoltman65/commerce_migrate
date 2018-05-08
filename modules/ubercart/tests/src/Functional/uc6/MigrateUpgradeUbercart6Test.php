<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Functional\uc6;

use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate_drupal\MigrationConfigurationTrait;
use Drupal\Tests\migrate_drupal_ui\Functional\MigrateUpgradeTestBase;

/**
 * Tests Ubercart 6 using the migrate UI.
 *
 * The test method is provided by the MigrateUpgradeTestBase class.
 *
 * @requires module migrate_plus
 * @requires module commerce_shipping
 * @requires module physical
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc6
 */
class MigrateUpgradeUbercart6Test extends MigrateUpgradeTestBase {

  use MigrationConfigurationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'block',
    'block_content',
    'comment',
    'dblog',
    'field',
    'filter',
    'node',
    'path',
    'search',
    'shortcut',
    'system',
    'taxonomy',
    'user',
    'address',
    'commerce',
    'commerce_cart',
    'commerce_log',
    'commerce_order',
    'commerce_payment',
    'commerce_price',
    'commerce_product',
    'commerce_promotion',
    'commerce_shipping',
    'commerce_store',
    'commerce_tax',
    'commerce_migrate',
    'migrate',
    'migrate_drupal',
    'migrate_drupal_ui',
    'address',
    'datetime',
    'entity_reference_revisions',
    'file',
    'image',
    'link',
    'options',
    'telephone',
    'text',
    'entity',
    'physical',
    'profile',
    'inline_entity_form',
    'state_machine',
    'views',
    'migrate_plus',
    'commerce_migrate_ubercart',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->loadFixture(drupal_get_path('module', 'commerce_migrate_ubercart') . '/tests/fixtures/uc6.php');
  }

  /**
   * Executes all steps of migrations upgrade.
   */
  public function testMigrateUpgrade() {
    $connection_options = $this->sourceDatabase->getConnectionOptions();
    $session = $this->assertSession();

    $driver = $connection_options['driver'];
    $connection_options['prefix'] = $connection_options['prefix']['default'];

    // Use the driver connection form to get the correct options out of the
    // database settings. This supports all of the databases we test against.
    $drivers = drupal_get_database_types();
    $form = $drivers[$driver]->getFormOptions($connection_options);
    $connection_options = array_intersect_key($connection_options, $form + $form['advanced_options']);
    $version = $this->getLegacyDrupalVersion($this->sourceDatabase);
    $edit = [
      $driver => $connection_options,
      'source_private_file_path' => $this->getSourceBasePath(),
      'version' => $version,
    ];
    if ($version == 6) {
      $edit['d6_source_base_path'] = $this->getSourceBasePath();
    }
    else {
      $edit['source_base_path'] = $this->getSourceBasePath();
    }
    if (count($drivers) !== 1) {
      $edit['driver'] = $driver;
    }
    $edits = $this->translatePostValues($edit);

    // Start the upgrade process.
    $this->drupalGet('/upgrade');

    $this->drupalPostForm(NULL, [], t('Continue'));
    $session->pageTextContains('Provide credentials for the database of the Drupal site you want to upgrade.');
    $session->fieldExists('mysql[host]');

    $this->drupalPostForm(NULL, $edits, t('Review upgrade'));
    $session->statusCodeEquals(200);

    $this->drupalPostForm(NULL, [], t('Perform upgrade'));

    // Have to reset all the statics after migration to ensure entities are
    // loadable.
    $this->resetAll();

    $expected_counts = $this->getEntityCounts();
    foreach (array_keys(\Drupal::entityTypeManager()->getDefinitions()) as $entity_type) {
      $real_count = \Drupal::entityQuery($entity_type)->count()->execute();
      $expected_count = isset($expected_counts[$entity_type]) ? $expected_counts[$entity_type] : 0;
      $this->assertEqual($expected_count, $real_count, "Found $real_count $entity_type entities, expected $expected_count.");
    }

    $plugin_manager = \Drupal::service('plugin.manager.migration');
    /** @var \Drupal\migrate\Plugin\Migration[] $all_migrations */
    $all_migrations = $plugin_manager->createInstancesByTag('Drupal ' . $version);
    foreach ($all_migrations as $migration) {
      $id_map = $migration->getIdMap();
      foreach ($id_map as $source_id => $map) {
        // Convert $source_id into a keyless array so that
        // \Drupal\migrate\Plugin\migrate\id_map\Sql::getSourceHash() works as
        // expected.
        $source_id_values = array_values(unserialize($source_id));
        $row = $id_map->getRowBySource($source_id_values);
        $destination = serialize($id_map->currentDestination());
        $message = "Migration of $source_id to $destination as part of the {$migration->id()} migration. The source row status is " . $row['source_row_status'];
        // A completed migration should have maps with
        // MigrateIdMapInterface::STATUS_IGNORED or
        // MigrateIdMapInterface::STATUS_IMPORTED.
        if ($row['source_row_status'] == MigrateIdMapInterface::STATUS_FAILED || $row['source_row_status'] == MigrateIdMapInterface::STATUS_NEEDS_UPDATE) {
          $this->fail($message);
        }
        else {
          $this->pass($message);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function getSourceBasePath() {
    return __DIR__ . '/files';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityCounts() {
    return [
      'block' => '24',
      'block_content' => '0',
      'block_content_type' => '1',
      'comment' => '0',
      'comment_type' => '3',
      'commerce_log' => '0',
      'commerce_order_type' => '1',
      'commerce_order' => '4',
      'commerce_order_item' => '5',
      'commerce_order_item_type' => '1',
      'commerce_payment' => '4',
      'commerce_payment_method' => '0',
      'commerce_payment_gateway' => '2',
      'commerce_product' => '5',
      'commerce_product_attribute' => '4',
      'commerce_product_attribute_value' => '7',
      'commerce_product_variation_type' => '1',
      'commerce_product_variation' => '7',
      'commerce_product_type' => '4',
      'commerce_promotion_coupon' => '0',
      'commerce_promotion' => '0',
      'commerce_currency' => '2',
      'commerce_shipping_method' => '2',
      'commerce_shipment_type' => '1',
      'commerce_store' => '1',
      'commerce_store_type' => '1',
      'commerce_tax_type' => '1',
      'contact_form' => '2',
      'contact_message' => '0',
      'editor' => '2',
      'field_config' => '41',
      'field_storage_config' => '27',
      'file' => '0',
      'filter_format' => '5',
      'image_style' => '3',
      'migration_group' => '1',
      'migration' => '0',
      'node_type' => '3',
      'node' => '1',
      'profile_type' => '1',
      'profile' => '4',
      'rdf_mapping' => '5',
      'search_page' => '2',
      'shortcut' => '2',
      'shortcut_set' => '1',
      'action' => '29',
      'menu' => '8',
      'taxonomy_vocabulary' => '2',
      'taxonomy_term' => '3',
      'tour' => '1',
      'user_role' => '5',
      'user' => '6',
      'menu_link_content' => '0',
      'view' => '24',
      'base_field_override' => '6',
      'date_format' => '11',
      'entity_view_display' => '29',
      'entity_view_mode' => '17',
      'entity_form_display' => '18',
      'entity_form_mode' => '2',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityCountsIncremental() {}

  /**
   * {@inheritdoc}
   */
  protected function getAvailablePaths() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function getMissingPaths() {
    return [];
  }

}
