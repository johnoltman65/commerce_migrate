<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\commerce_payment\Entity\PaymentGateway;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests payment migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class PaymentTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'path',
    'commerce_product',
    'commerce_payment',
    'migrate_plus',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('view');
    $this->installEntitySchema('profile');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installEntitySchema('commerce_payment');
    $this->installConfig(['commerce_order']);
    $this->installConfig(['commerce_product']);
    $this->installConfig(['commerce_payment']);

    PaymentGateway::create([
      'id' => 'example',
      'label' => 'Example',
      'plugin' => 'manual',
    ])->save();

    $this->migrateStore();
    $this->executeMigrations([
      'd6_ubercart_billing_profile',
      'd6_ubercart_order',
      'd6_ubercart_payment_gateway',
      'd6_ubercart_payment',
    ]);

  }

  /**
   * Tests payment migration.
   */
  public function testPayment() {
    $payment = [
      'id' => 1,
      'order_id' => '1',
      'type' => 'payment_manual',
      'payment_gateway' => 'check',
      'payment_method' => NULL,
      'amount_number' => '37.990000',
      'amount_currency_code' => 'NZD',
      'refunded_amount_number' => '0.000000',
      'refunded_amount_currency_code' => 'NZD',
      'balance_number' => '37.99',
      'balance_currency_code' => 'NZD',
      'label_value' => 'new',
      'label_rendered' => 'New',
    ];
    $this->assertPaymentEntity($payment);
    $payment = [
      'id' => 2,
      'order_id' => '2',
      'type' => 'payment_manual',
      'payment_gateway' => 'cod',
      'payment_method' => NULL,
      'amount_number' => '2000.000000',
      'amount_currency_code' => 'NZD',
      'refunded_amount_number' => '1700.000000',
      'refunded_amount_currency_code' => 'NZD',
      'balance_number' => '300',
      'balance_currency_code' => 'NZD',
      'label_value' => 'partially_refunded',
      'label_rendered' => 'Partially refunded',
    ];
    $this->assertPaymentEntity($payment);
    $payment = [
      'id' => 4,
      'order_id' => '2',
      'type' => 'payment_manual',
      'payment_gateway' => 'cod',
      'payment_method' => NULL,
      'amount_number' => '50.000000',
      'amount_currency_code' => 'NZD',
      'refunded_amount_number' => '0.000000',
      'refunded_amount_currency_code' => 'NZD',
      'balance_number' => '50',
      'balance_currency_code' => 'NZD',
      'label_value' => 'new',
      'label_rendered' => 'New',
    ];
    $this->assertPaymentEntity($payment);
    $payment = [
      'id' => 6,
      'order_id' => '3',
      'type' => 'payment_manual',
      'payment_gateway' => 'cod',
      'payment_method' => NULL,
      'amount_number' => '12.000000',
      'amount_currency_code' => 'NZD',
      'refunded_amount_number' => '12.000000',
      'refunded_amount_currency_code' => 'NZD',
      'balance_number' => '0',
      'balance_currency_code' => 'NZD',
      'label_value' => 'refunded',
      'label_rendered' => 'Refunded',
    ];
    $this->assertPaymentEntity($payment);

    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = $this->getMigration('d6_ubercart_payment');

    // Check that we've reported the refund in excess of payments.
    $messages = [];
    foreach ($migration->getIdMap()->getMessageIterator() as $message_row) {
      $messages[] = $message_row->message;
    }
    $this->assertCount(1, $messages);
    $this->assertSame('Refund exceeds payments for payment 6', $messages[0]);
  }

}
