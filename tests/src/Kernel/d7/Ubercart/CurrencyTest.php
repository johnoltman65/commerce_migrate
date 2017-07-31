<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Ubercart;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests currency migration.
 *
 * @group commerce_migrate
 */
class CurrencyTest extends Ubercart7TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->executeMigration('ubercart_currency');
  }

  /**
   * Test currency migration from Drupal 7 to 8.
   */
  public function testCurrency() {
    /** @var \Drupal\commerce_price\Entity\CurrencyInterface $currency */
    $this->assertCurrencyEntity('USD', 'USD', 'US Dollar', '840', 2, '$');
  }

}
