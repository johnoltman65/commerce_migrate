<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\Tests\migrate_drupal\Kernel\d6\MigrateDrupal6TestBase;

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
  ];

  /**
   * @inheritDoc
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

}
