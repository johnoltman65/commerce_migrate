<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\commerce1;

use Drupal\commerce_order\Entity\Order;
use Drupal\profile\Entity\Profile;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests order migration.
 *
 * @requires migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce1
 */
class OrderTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'commerce_order',
    'commerce_price',
    'commerce_product',
    'commerce_store',
    'migrate_plus',
    'path',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->migrateOrderswithCart();
  }

  /**
   * Test line item migration from Drupal 7 to 8.
   */
  public function testOrder() {
    $order = [
      'id' => 1,
      'type' => 'default',
      'number' => '1',
      'store_id' => '1',
      'created_time' => '1493287432',
      // Changed time is overwritten by Commerce when the status is Draft. The
      // source changed time is '1508452606'.
      'changed_time' => '1508452606',
      'completed_time' => NULL,
      'email' => 'customer@example.com',
      'ip_address' => '127.0.0.1',
      'customer_id' => '4',
      'placed_time' => NULL,
      'total_price' => '39.000000',
      'total_price_currency' => 'USD',
      'adjustments' => [],
      'label_value' => 'draft',
      'label_rendered' => 'Draft',
      'order_items_ids' => ['1', '11', '2'],
      'billing_profile' => ['4', '4'],
      'data' => [
        'profiles' => [
          'customer_profile_billing' => '1',
          'customer_profile_shipping' => '2',
        ],
        'paid_event_dispatched' => FALSE,
      ],
      'cart' => '1',
    ];
    $this->assertOrder($order);
    $order = [
      'id' => 2,
      'type' => 'default',
      'number' => '2',
      'store_id' => '1',
      'created_time' => '1493287435',
      'changed_time' => '1508452654',
      'completed_time' => '1508452654',
      'email' => 'customer@example.com',
      'ip_address' => '127.0.0.1',
      'customer_id' => '4',
      'placed_time' => '1493287435',
      'total_price' => '120.000000',
      'total_price_currency' => 'USD',
      'adjustments' => [],
      'label_value' => 'completed',
      'label_rendered' => 'Completed',
      'order_items_ids' => ['3', '4', '5', '6', '7', '12'],
      'billing_profile' => ['6', '6'],
      'data' => [
        'profiles' => [
          'customer_profile_billing' => '1',
          'customer_profile_shipping' => '2',
        ],
        'payment_method' => 'commerce_payment_example|commerce_payment_commerce_payment_example',
        'commerce_payment_example' => [
          'credit_card' => [
            'number' => '4111111111111111',
            'exp_month' => '06',
            'exp_year' => '2012',
          ],
        ],
        'commerce_payment_order_paid_in_full_invoked' => TRUE,
      ],
      'cart' => '0',
    ];
    $this->assertOrder($order);
    $order = [
      'id' => 3,
      'type' => 'default',
      'number' => '3',
      'store_id' => '1',
      'created_time' => '1493287438',
      'changed_time' => '1508452668',
      'completed_time' => '1508452668',
      'email' => 'customer@example.com',
      'ip_address' => '127.0.0.1',
      'customer_id' => '4',
      'placed_time' => '1493287438',
      'total_price' => '41.490000',
      'total_price_currency' => 'USD',
      'adjustments' => [],
      'label_value' => 'completed',
      'label_rendered' => 'Completed',
      'order_items_ids' => ['13', '8', '9', '10'],
      'billing_profile' => ['8', '8'],
      'data' => [
        'profiles' => [
          'customer_profile_billing' => '1',
          'customer_profile_shipping' => '2',
        ],
        'payment_method' => 'commerce_payment_example|commerce_payment_commerce_payment_example',
        'commerce_payment_example' => [
          'credit_card' => [
            'number' => '4111111111111111',
            'exp_month' => '06',
            'exp_year' => '2012',
          ],
        ],
        'commerce_payment_order_paid_in_full_invoked' => TRUE,
      ],
      'cart' => '0',
    ];
    $this->assertOrder($order);

    // Test billing profile.
    $order = Order::load(1);
    $profile = $order->getBillingProfile();
    $this->assertInstanceOf(Profile::class, $profile);
    $this->assertEquals($profile->bundle(), 'customer');
    $this->assertEquals($profile->isActive(), TRUE);

    // Test store.
    $this->assertEquals(\Drupal::service('commerce_store.default_store_resolver')
      ->resolve()
      ->id(), $order->getStoreId());
  }

}
