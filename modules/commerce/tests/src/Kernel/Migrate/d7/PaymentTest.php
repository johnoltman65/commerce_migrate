<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\commerce_payment\Entity\Payment;

/**
 * Tests payment migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class PaymentTest extends Commerce1TestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'path',
    'commerce_product',
    'commerce_payment',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('view');
    $this->installEntitySchema('profile');
    $this->installEntitySchema('commerce_product_variation');
    $this->installEntitySchema('commerce_order');
    $this->installEntitySchema('commerce_order_item');
    $this->installEntitySchema('commerce_payment');
    $this->installConfig(['commerce_order']);
    $this->migrateStore();
    // @todo Execute the d7_field and d7_field_instance migrations?
    $this->executeMigrations([
      'd7_user_role',
      'd7_user',
      'd7_commerce_product_variation_type',
      'd7_commerce_product_variation',
      'd7_commerce_billing_profile',
      'd7_commerce_order_item_type',
      'd7_commerce_order_item',
      'd7_commerce_order',
      'd7_commerce_payment_gateway',
      'd7_commerce_payment',
    ]);
  }

  /**
   * Asserts a payment entity.
   *
   * @param array $payment
   *   An array of payment information.
   *   - The payment id.
   *   - The order id for this payment.
   *   - The payment type.
   *   - The gateway id.
   *   - The payment method.
   *   - The payment amount.
   *   - The payment currency code.
   *   - The order balance.
   *   - The order balance currency code.
   *   - The refunded amount.
   *   - The refunded amount currency code.
   */
  private function assertPaymentEntity($payment) {
    $payment_instance = Payment::load($payment['id']);
    $this->assertInstanceOf(Payment::class, $payment_instance);
    $this->assertSame($payment['order_id'], $payment_instance->getOrderId());
    $this->assertSame($payment['type'], $payment_instance->getType()->getPluginId());
    $this->assertSame($payment['payment_gateway'], $payment_instance->getPaymentGatewayId());
    $this->assertSame($payment['payment_method'], $payment_instance->getPaymentMethodId());
    $this->assertSame($payment['amount_number'], $payment_instance->getAmount()->getNumber());
    $this->assertSame($payment['amount_currency_code'], $payment_instance->getAmount()->getCurrencyCode());
    $this->assertSame($payment['balance_number'], $payment_instance->getBalance()->getNumber());
    $this->assertSame($payment['balance_currency_code'], $payment_instance->getBalance()->getCurrencyCode());
    $this->assertSame($payment['refunded_amount_number'], $payment_instance->getRefundedAmount()->getNumber());
    $this->assertSame($payment['refunded_amount_currency_code'], $payment_instance->getRefundedAmount()->getCurrencyCode());
    $this->assertSame($payment['label_value'], $payment_instance->getState()->value);
    $state_label = $payment_instance->getState()->getLabel();
    $label = NULL;
    if (is_string($state_label)) {
      $label = $state_label;
    }
    elseif ($state_label instanceof TranslatableMarkup) {
      $arguments = $state_label->getArguments();
      $label = isset($arguments['@label']) ? $arguments['@label'] : $state_label->render();
    }
    $this->assertSame($payment['label_rendered'], $label);
  }

  /**
   * Test line item migration from Drupal 7 to 8.
   */
  public function testPayment() {
    $payment = [
      'id' => 1,
      'order_id' => '2',
      'type' => 'payment_manual',
      'payment_gateway' => 'commerce_payment_example',
      'payment_method' => NULL,
      'amount_number' => '12000.000000',
      'amount_currency_code' => 'USD',
      'refunded_amount_number' => '0.000000',
      'refunded_amount_currency_code' => 'USD',
      'balance_number' => '12000',
      'balance_currency_code' => 'USD',
      'label_value' => 'success',
      'label_rendered' => 'success',
      'created' => '1493287432',
      'changed' => '1493287450',
    ];
    $this->assertPaymentEntity($payment);
    $payment = [
      'id' => 2,
      'order_id' => '3',
      'type' => 'payment_manual',
      'payment_gateway' => 'commerce_payment_example',
      'payment_method' => NULL,
      'amount_number' => '3999.000000',
      'amount_currency_code' => 'USD',
      'refunded_amount_number' => '0.000000',
      'refunded_amount_currency_code' => 'USD',
      'balance_number' => '3999',
      'balance_currency_code' => 'USD',
      'label_value' => 'success',
      'label_rendered' => 'success',
      'created' => '1493287432',
      'changed' => '1493287450',
    ];
    $this->assertPaymentEntity($payment);
  }

}
