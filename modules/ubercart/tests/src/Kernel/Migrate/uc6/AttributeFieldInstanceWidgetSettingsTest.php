<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc6;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface;

/**
 * Tests attribute field instance widget settings migration.
 *
 * @requires module migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc6
 */
class AttributeFieldInstanceWidgetSettingsTest extends Ubercart6TestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'commerce_product',
    'path',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('commerce_product_variation');
    $this->installConfig(['commerce_product']);
    $this->executeMigrations([
      'uc6_attribute_field',
      'uc6_product_attribute',
      'uc6_attribute_field_instance',
      'uc6_attribute_instance_widget_settings',
    ]);
  }

  /**
   * Asserts various aspects of a form display entity.
   *
   * @param string $id
   *   The entity ID.
   * @param string $expected_entity_type
   *   The expected entity type to which the display settings are attached.
   * @param string $expected_bundle
   *   The expected bundle to which the display settings are attached.
   */
  protected function assertEntity($id, $expected_entity_type, $expected_bundle) {
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $entity */
    $entity = EntityFormDisplay::load($id);
    $this->assertInstanceOf(EntityFormDisplayInterface::class, $entity);
    $this->assertSame($expected_entity_type, $entity->getTargetEntityTypeId());
    $this->assertSame($expected_bundle, $entity->getTargetBundle());
  }

  /**
   * Asserts various aspects of a particular component of a form display.
   *
   * @param string $display_id
   *   The form display ID.
   * @param string $component_id
   *   The component ID.
   * @param string $widget_type
   *   The expected widget type.
   * @param string $weight
   *   The expected weight of the component.
   */
  protected function assertComponent($display_id, $component_id, $widget_type, $weight) {
    $component = EntityFormDisplay::load($display_id)->getComponent($component_id);
    $this->assertTrue(is_array($component));
    $this->assertSame($widget_type, $component['type']);
    $this->assertSame($weight, $component['weight']);
  }

  /**
   * Test the migration of the attributes on the prouct variation form display.
   */
  public function testAttributeWidgetSettings() {
    $this->assertEntity('commerce_product_variation.default.default', 'commerce_product_variation', 'default');
    $this->assertComponent('commerce_product_variation.default.default', 'attribute_color', 'boolean_checkbox', 500);
    $this->assertComponent('commerce_product_variation.default.default', 'attribute_design', 'options_buttons', 0);
    $this->assertComponent('commerce_product_variation.default.default', 'attribute_model_size_attribute', 'options_select', 400);

    $display = EntityFormDisplay::load('commerce_product_variation.default.default');
    $this->assertInstanceOf(EntityFormDisplay::class, $display);
    $component = $display->getComponent('attribute_name');
    $this->assertNull($component);
  }

}