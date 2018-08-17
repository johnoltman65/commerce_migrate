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
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'path',
    'commerce_product',
    'migrate_plus',
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
    $this->installConfig(['commerce_order']);
    $this->migrateStore();
    // @todo Execute the d7_field and d7_field_instance migrations?
    $this->executeMigrations([
      'd7_user_role',
      'd7_user',
      'commerce1_product_variation_type',
      'commerce1_product_variation',
      'commerce1_billing_profile',
      'commerce1_order_item_type',
      'commerce1_order_item',
      'commerce1_order',
    ]);
  }

  /**
   * Test line item migration from Drupal 7 to 8.
   */
  public function testOrder() {
    $order = Order::load(1);
    // Test line items.
    $order_items = $order->getItems();
    $this->assertNotNull($order_items);
    $this->assertEquals('Hat 2', $order_items[0]->label());
    $this->assertEquals('Hat 2', $order_items[1]->label());
    $this->assertEquals(39.000000, $order->getTotalPrice()->getNumber());

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
      'data' => unserialize('a:1:{s:8:"profiles";a:2:{s:24:"customer_profile_billing";s:1:"1";s:25:"customer_profile_shipping";s:1:"2";}}'),
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
      'data' => unserialize('a:4:{s:8:"profiles";a:2:{s:24:"customer_profile_billing";s:1:"1";s:25:"customer_profile_shipping";s:1:"2";}s:14:"payment_method";s:66:"commerce_payment_example|commerce_payment_commerce_payment_example";s:24:"commerce_payment_example";a:1:{s:11:"credit_card";a:3:{s:6:"number";s:16:"4111111111111111";s:9:"exp_month";s:2:"06";s:8:"exp_year";s:4:"2012";}}s:43:"commerce_payment_order_paid_in_full_invoked";b:1;}'),
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
      'data' => unserialize('a:4:{s:8:"profiles";a:2:{s:24:"customer_profile_billing";s:1:"1";s:25:"customer_profile_shipping";s:1:"2";}s:14:"payment_method";s:66:"commerce_payment_example|commerce_payment_commerce_payment_example";s:24:"commerce_payment_example";a:1:{s:11:"credit_card";a:3:{s:6:"number";s:16:"4111111111111111";s:9:"exp_month";s:2:"06";s:8:"exp_year";s:4:"2012";}}s:43:"commerce_payment_order_paid_in_full_invoked";b:1;}'),
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
