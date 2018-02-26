<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\Tests\migrate_drupal\Kernel\d6\MigrateDrupal6TestBase;
use Drupal\migrate\MigrateExecutable;

/**
 * Test base for Ubercart D6 tests.
 */
abstract class Ubercart6TestBase extends MigrateDrupal6TestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'action',
    'address',
    'commerce',
    'commerce_price',
    'commerce_store',
    'commerce_order',
    'commerce_migrate',
    'entity',
    'entity_reference_revisions',
    'inline_entity_form',
    'profile',
    'state_machine',
    'text',
    'views',
    'commerce_migrate_ubercart',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('commerce_store');
  }

  /**
   * Executes store migrations.
   */
  protected function migrateStore() {
    $this->migrateUsers(FALSE);
    $this->executeMigrations([
      'ubercart_currency',
      'd6_ubercart_store',
    ]);
  }

  /**
   * Gets the path to the fixture file.
   */
  protected function getFixtureFilePath() {
    return __DIR__ . '/../../../../fixtures/uc6.php';
  }

  /**
   * Executes rollback on single migration.
   *
   * @param string|\Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration to rollback, or its ID.
   */
  protected function executeRollback($migration) {
    if (is_string($migration)) {
      $this->migration = $this->getMigration($migration);
    }
    else {
      $this->migration = $migration;
    }
    (new MigrateExecutable($this->migration, $this))->rollback();
  }

}
