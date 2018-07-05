<?php

namespace Drupal\Tests\commerce_migrate_magento\Kernel\Migrate;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;
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

  /**
   * Prepare the file migration for running.
   */
  protected function fileMigrationSetup() {
    $this->installSchema('file', ['file_usage']);
    $this->installEntitySchema('file');
    $this->container->get('stream_wrapper_manager')
      ->registerWrapper('public', PublicStream::class, StreamWrapperInterface::NORMAL);

    // The public file directory active during the test will serve as the
    // source for the image files.
    // @todo: Get the directory from the migration
    $destination = 'public://import/images';

    // Copy all test source files to source directory used in migration.
    $source_directory = __DIR__ . '/../../../fixtures/images/';
    $this->recurseCopy($source_directory, $destination);
  }

  /**
   * Helper to copy directory tree.
   *
   * @param string $src
   *   The source path.
   * @param string $dst
   *   The destination path.
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
          file_unmanaged_copy($src . '/' . $file, $dst . '/' . $file);
        }
      }
    }
    closedir($dir);
  }

}
