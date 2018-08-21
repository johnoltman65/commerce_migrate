<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc7;

use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

/**
 * Test base for Ubercart 7 tests.
 */
abstract class Ubercart7TestBase extends MigrateDrupal7TestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    // Commerce requirements.
    'address',
    'commerce',
    'entity',
    'entity_reference_revisions',
    'inline_entity_form',
    'views',
    // Commerce migrate requirements.
    'commerce_migrate',
    'commerce_migrate_ubercart',
  ];

  /**
   * Gets the path to the fixture file.
   */
  protected function getFixtureFilePath() {
    return __DIR__ . '/../../../../fixtures/uc7.php';
  }

  /**
   * Executes all user migrations.
   */
  protected function migrateUsers() {
    $this->executeMigrations(['d7_user_role', 'd7_user']);
  }

  /**
   * Executes store migration.
   *
   * Required modules:
   * - commerce_price.
   * - commerce_store.
   */
  protected function migrateStore() {
    $this->installEntitySchema('commerce_store');
    $this->migrateUsers();
    $this->executeMigrations([
      'uc_currency',
      'uc7_store',
    ]);
  }

}
