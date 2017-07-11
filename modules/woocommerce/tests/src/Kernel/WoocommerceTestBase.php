<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\Tests\migrate\Kernel\MigrateTestBase;

/**
 * Base class for Woocommerce migration tests.
 */
abstract class WoocommerceTestBase extends MigrateTestBase {

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
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('commerce_store');
  }

  /**
   * Gets the path to the fixture file.
   */
  protected function getFixtureFilePath() {
    return __DIR__ . '/../../../../fixtures/woocommerce.php';
  }

}
