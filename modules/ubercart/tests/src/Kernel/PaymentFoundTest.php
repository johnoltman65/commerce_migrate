<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel;

use Drupal\Tests\migrate_drupal\Kernel\MigrateDrupalTestBase;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;

/**
 * Tests that modules exist for all source and destination plugins.
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class PaymentFoundTest extends MigrateDrupalTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'commerce_migrate_ubercart',
  ];

  /**
   * Payment migrations to test.
   *
   * @var array
   */
  protected $paymentMigrations = [
    'd6_ubercart_payment_gateway',
    'd6_ubercart_payment',
  ];

  /**
   * Tests paymnet migration discovery when commerce_payment is installed.
   */
  public function testPaymentMigrationsFound() {
    $this->enableModules(['commerce_price', 'commerce']);
    /** @var \Drupal\migrate\Plugin\MigrationPluginManager $plugin_manager */
    $plugin_manager = $this->container->get('plugin.manager.migration');
    foreach ($this->paymentMigrations as $payment_migration) {
      $definition = $plugin_manager->getDefinition($payment_migration);
      $migration = $plugin_manager->createInstance($payment_migration, $definition);
      $this->assertNotNull($migration, "Payment migration " . $payment_migration . " not found");
    }
  }

  /**
   * Tests paymnet migration discovery when commerce_payment is not installed.
   */
  public function testPaymentMigrationsNotFound() {
    /** @var \Drupal\migrate\Plugin\MigrationPluginManager $plugin_manager */
    $plugin_manager = $this->container->get('plugin.manager.migration');
    foreach ($this->paymentMigrations as $payment_migration) {
      $this->setExpectedException(PluginNotFoundException::class, 'The "' . $payment_migration . '" plugin does not exist.');
      $plugin_manager->getDefinition($payment_migration);
    }
  }

}
