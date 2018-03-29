<?php

namespace Drupal\Tests\commerce_migrate_magento\Kernel\Migrate;

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
   * File path of the test fixture.
   *
   * @var string
   */
  protected $fixture;

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
    return $this->fixture;
  }

  /**
   * Copy the source CSV to the path in the migration.
   *
   * @param string $fixture
   *   The full pathname of the fixture.
   */
  protected function loadFixture($fixture) {
    // Make sure the file destination directory exists.
    $fixturePath = dirname($fixture);
    if (!file_exists($fixturePath)) {
      $this->fs->mkdir($fixturePath, NULL, TRUE);
    }

    // Copy fixture.
    $filename = basename($fixture);
    $source = __DIR__ . '/../../../fixtures/csv/' . $filename;
    $destination_uri = $fixture;
    file_unmanaged_copy($source, $destination_uri);
  }

}
