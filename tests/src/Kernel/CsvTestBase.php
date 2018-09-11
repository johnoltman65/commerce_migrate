<?php

namespace Drupal\Tests\commerce_migrate\Kernel;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\migrate\MigrateException;
use Drupal\Tests\migrate\Kernel\MigrateTestBase;

/**
 * Test base for migrations tests with CSV source file.
 *
 * Any migration using this test base must set the 'path' property to the same
 * as $csvPath, 'public://import'. The source test CSV file must be in
 * /tests/fixtures/csv and any source file to migrate, such as images, must be
 * in /test/fixtures/images.
 */
abstract class CsvTestBase extends MigrateTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'system',
    'migrate_source_csv',
  ];

  /**
   * Basename of the directory used in the migration 'path:' configuration.
   *
   * The basename must be the same for all migrations in a test.
   *
   * @var string
   */
  protected $csvPath = 'public://import';

  /**
   * The relative path to each test fixture needed for the test.
   *
   * @var string|array
   */
  protected $fixtures;

  /**
   * File system active during the test.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fs;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    KernelTestBase::setUp();
    // Setup a public file directory for all migration source files.
    $this->fs = $this->container->get('file_system');
    $this->config('system.file')->set('default_scheme', 'public')->save();
    $this->loadFixture($this->getFixtureFilePath());
  }

  /**
   * Gets the path to the fixture file.
   */
  protected function getFixtureFilePath() {
    return $this->fixtures;
  }

  /**
   * Copy the source CSV files to the path in the migration.
   *
   * @param string|array $fixtures
   *   The full pathname of the fixture.
   */
  protected function loadFixture($fixtures) {
    if (is_string($fixtures)) {
      $fixtures = [$fixtures];
    }

    // Make sure the file destination directory exists.
    if (!file_exists($this->csvPath)) {
      $this->fs->mkdir($this->csvPath, NULL, TRUE);
    }

    // Copy each fixture to the public directory.
    foreach ($fixtures as $fixture) {
      $filename = basename($fixture);
      $destination_uri = $this->csvPath . '/' . $filename;
      if (!file_unmanaged_copy($fixture, $destination_uri)) {
        throw new MigrateException("Migration setup failed to copy source CSV file '$fixture' to '$destination_uri'.");
      }
    }
  }

  /**
   * Prepares a public file directory for the migration.
   *
   * Enables file module and recursively copies the source directory to the
   * migration source path.
   *
   * @param string $source_directory
   *   The source file directory.
   */
  protected function fileMigrationSetup($source_directory) {
    $this->installSchema('file', ['file_usage']);
    $this->installEntitySchema('file');
    $this->container->get('stream_wrapper_manager')
      ->registerWrapper('public', PublicStream::class, StreamWrapperInterface::NORMAL);
    // Copy the file source directory to the public directory.
    $destination = $this->csvPath . '/images';
    $this->recurseCopy($source_directory, $destination);
  }

  /**
   * Helper to copy directory tree.
   *
   * @param string $src
   *   The source path.
   * @param string $dst
   *   The destination path.
   *
   * @throws \Drupal\migrate\MigrateException
   */
  private function recurseCopy($src, $dst) {
    $dir = opendir($src);
    if (!file_exists($dst)) {
      $this->fs->mkdir($dst, NULL, TRUE);
    }
    while (FALSE !== ($file = readdir($dir))) {
      if (($file != '.') && ($file != '..')) {
        if (is_dir($src . '/' . $file)) {
          $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
        }
        else {
          if (!file_unmanaged_copy($src . '/' . $file, $dst . '/' . $file)) {
            closedir($dir);
            throw new MigrateException("Migration setup failed to copy source file '$src' to '$dst'.");
          }
        }
      }
    }
    closedir($dir);
  }

  /**
   * Creates a default store.
   */
  protected function createDefaultStore() {
    $currency_importer = \Drupal::service('commerce_price.currency_importer');
    /** @var \Drupal\commerce_store\StoreStorage $store_storage */
    $store_storage = \Drupal::service('entity_type.manager')
      ->getStorage('commerce_store');

    $currency_importer->import('USD');
    $store_values = [
      'type' => 'default',
      'uid' => 1,
      'name' => 'Demo store',
      'mail' => 'admin@example.com',
      'address' => [
        'country_code' => 'US',
      ],
      'default_currency' => 'USD',
    ];

    /** @var \Drupal\commerce_store\Entity\StoreInterface $store */
    $store = $store_storage->create($store_values);
    $store->save();
    $store_storage->markAsDefault($store);
  }

}
