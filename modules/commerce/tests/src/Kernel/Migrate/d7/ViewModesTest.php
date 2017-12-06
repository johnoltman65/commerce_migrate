<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\d7;

use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\Core\Entity\EntityViewModeInterface;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests commerce field view mode migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce_d7
 */
class MigrateViewModesTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'comment',
    'datetime',
    'file',
    'image',
    'link',
    'node',
    'system',
    'taxonomy',
    'telephone',
    'text',
    'path',
    'commerce_product',
    'migrate_plus',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(static::$modules);
    $this->executeMigrations([
      'd7_commerce_product_variation_type',
      'd7_view_modes',
    ]);
  }

  /**
   * Asserts various aspects of a view mode entity.
   *
   * @param string $id
   *   The entity ID.
   * @param string $label
   *   The expected label of the view mode.
   * @param string $entity_type
   *   The expected entity type ID which owns the view mode.
   */
  protected function assertEntity($id, $label, $entity_type) {
    $view_mode = EntityViewMode::load($id);
    $this->assertTrue($view_mode instanceof EntityViewModeInterface);
    $this->assertSame($label, $view_mode->label());
    $this->assertSame($entity_type, $view_mode->getTargetType());
  }

  /**
   * Tests migration of D7 view mode variables to D8 config entities.
   */
  public function testMigration() {
    $this->assertEntity('commerce_product_variation.add_to_cart_confirmation_view', 'add_to_cart_confirmation_view', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.commerce_line_item_display', 'commerce_line_item_display', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.full', 'Full', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.line_item', 'line_item', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.node_full', 'node_full', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.node_product_list', 'node_product_list', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.node_rss', 'node_rss', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.node_search_index', 'node_search_index', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.node_search_result', 'node_search_result', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.node_teaser', 'node_teaser', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.product_in_cart', 'product_in_cart', 'commerce_product_variation');
    $this->assertEntity('commerce_product_variation.add_to_cart_confirmation_view', 'add_to_cart_confirmation_view', 'commerce_product_variation');
  }

}
