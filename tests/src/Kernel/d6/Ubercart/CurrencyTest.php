<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\commerce_price\Entity\Currency;
use Drupal\commerce_price\Entity\CurrencyInterface;

/**
 * Tests currency migration.
 *
 * @group commerce_migrate
 */
class CurrencyTest extends Ubercart6TestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->executeMigration('ubercart_currency');
  }

  /**
   * Test currency migration from Drupal 6 to 8.
   */
  public function testCurrency() {
    /** @var \Drupal\commerce_price\Entity\CurrencyInterface $currency */
    $this->assertCurrencyEntity('NZD', 'NZD', 'New Zealand Dollar', '554', 2, '$');
  }

  /**
   * @param string $id
   *   The currency id.
   * @param $expected_currency_code
   * @param $expected_name
   * @param $expected_numeric_code
   * @param $expected_fraction_digits
   * @param $expected_symbol
   */
  public function assertCurrencyEntity($id, $expected_currency_code, $expected_name, $expected_numeric_code, $expected_fraction_digits, $expected_symbol) {
    /** @var \Drupal\commerce_price\Entity\CurrencyInterface $currency */
    $currency = Currency::load($id);
    $this->assertInstanceOf(CurrencyInterface::class, $currency);
    $this->assertSame($expected_currency_code, $currency->getCurrencyCode());
    $this->assertSame($expected_name, $currency->getName());
    $this->assertSame($expected_fraction_digits, $currency->getFractionDigits());
    $this->assertSame($expected_numeric_code, $currency->getNumericCode());
    $this->assertSame($expected_symbol, $currency->getSymbol());
  }

}
