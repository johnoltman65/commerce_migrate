<?php

namespace Drupal\Tests\commerce_migrate\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\migrate\Kernel\MigrateTestBase;

/**
 * Test base for migrations tests with CSV source file.
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

    // Copy each fixture.
    foreach ($fixtures as $fixture) {
      $filename = basename($fixture);
      $destination_uri = $this->csvPath . '/' . $filename;
      file_unmanaged_copy($fixture, $destination_uri);
    }
  }

}
