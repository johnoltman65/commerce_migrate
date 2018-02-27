<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;
use Drupal\commerce_log\Entity\Log;

/**
 * Tests message to log migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class LogTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'path',
    'commerce_log',
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
    $this->installEntitySchema('commerce_log');
    $this->installConfig(['commerce_order']);
    $this->migrateStore();
    $this->executeMigrations([
      'd7_user_role',
      'd7_user',
      'd7_commerce_product_variation_type',
      'd7_commerce_product_variation',
      'd7_commerce_billing_profile',
      'd7_commerce_order_item_type',
      'd7_commerce_order_item',
      'd7_commerce_order',
      'd7_commerce_message',
    ]);
  }

  /**
   * Asserts an Log entity.
   *
   * @param array $log
   *   An array of log information.
   *   - id: The log id.
   *   - category_id: The category id.
   *   - label: The label.
   *   - source_entity_id: The id of the source entity
   *   - created: The time the log entry was saved.
   *   - template_id: The template ID.
   */
  public function assertLog(array $log) {
    $log_instance = Log::load($log['id']);
    $this->assertInstanceOf(Log::class, $log_instance);
    $this->assertSame($log['category_id'], $log_instance->getCategoryId());
    $this->assertSame($log['label'], $log_instance->label());
    $this->assertSame($log['source_entity_id'], $log_instance->getSourceEntityId());
    $this->assertSame($log['created'], $log_instance->getCreatedTime());
    $this->assertSame($log['template_id'], $log_instance->getTemplateId());
  }

  /**
   * Test message migration from Drupal 7 to Drupal 8 Commerce Log.
   */
  public function testMessage() {
    // Test a 'commerce_order_created' message was migrated.
    $log = [
      'id' => 1,
      'category_id' => 'commerce_order',
      'label' => 'Order: Order placed',
      'source_entity_id' => '1',
      'created' => '1493287434',
      'template_id' => 'order_placed',
    ];
    $this->assertLog($log);

    // Test a 'commerce_order_cart_add' message was migrated.
    $log = [
      'id' => 2,
      'category_id' => 'commerce_cart',
      'label' => 'Cart: Added to cart',
      'source_entity_id' => '1',
      'created' => '1493287434',
      'template_id' => 'cart_entity_added',
    ];
    $this->assertLog($log);

  }

}
