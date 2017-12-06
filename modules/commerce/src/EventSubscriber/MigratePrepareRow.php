<?php

namespace Drupal\commerce_migrate_commerce\EventSubscriber;

use Drupal\field\Plugin\migrate\source\d7\Field;
use Drupal\field\Plugin\migrate\source\d7\FieldInstance;
use Drupal\migrate_plus\Event\MigrateEvents;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles migrate_plus prepare row event.
 *
 * @package Drupal\commerce_migrate_commerce\EventSubscriber
 */
class MigratePrepareRow implements EventSubscriberInterface {

  /**
   * Product node types.
   *
   * @var array
   */
  protected $productTypes = [];

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::PREPARE_ROW][] = 'prepareRow';
    return $events;
  }

  /**
   * Responds to prepare row event.
   *
   * @param \Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The event.
   */
  public function prepareRow(MigratePrepareRowEvent $event) {
    $migration = $event->getMigration();
    $row = $event->getRow();
    $source_plugin = $migration->getSourcePlugin();

    // Field source plugin.
    if (is_a($source_plugin, Field::class)) {
      // If the entity is 'node' the field may be for a product display, a non
      // product display or both. If the bundles for the field are
      // all product displays, then set the entity type to 'product_display'.
      // If the bundles are both product display and node then make the
      // 'additional' commerce field in the process pipeline. If the bundles
      // are all for node types then make no change to the migration.
      $this->productTypes = $this->getProductTypes($event);
      if ($row->getSourceProperty('entity_type') == 'node') {
        // Get the bundles for this field.
        $instances = $row->getSourceProperty('instances');
        $i = 0;
        foreach ($instances as $instance) {
          if (in_array($instance['bundle'], $this->productTypes)) {
            $i++;
          }
        }
        if ($i > 0) {
          if ($i == count($instances)) {
            // If all bundles for this field are product types, then change the
            // entity type to 'product_display'. This non existent entity type
            // will be changed by the entity_type static map configured in the
            // migration_plugins_alter.
            $row->setSourceProperty('entity_type', 'product_display');
          }
          else {
            // This field is used on both nodes and product displays. Set
            // commerce_entity_type so that field storage is created for the
            // commerce_product as well as for the node. When
            // commerce_entity_type is set the process plugin,
            // commerce_field_storage_entity_generate will create the
            // storage for the commerce entity.
            $row->setSourceProperty('commerce_entity_type', 'commerce_product');
          }
        }
      }
    }
    if (is_a($source_plugin, FieldInstance::class)) {
      // If the entity is 'node' the field may be for a product display, a non
      // product display or both. For now, if the bundles for the field are
      // all product displays, then set the entity type to 'product_display'.
      // If the field exists on both product displays and non product display
      // nodes only the node storage is created.
      if ($this->productTypes == []) {
        $this->productTypes = $this->getProductTypes($event);
      }
      if ($row->getSourceProperty('entity_type') == 'node') {
        // Get the bundles for this field.
        $bundle = $row->getSourceProperty('bundle');
        if (in_array($bundle, $this->getProductTypes($event))) {
          // If this is a field on a product display then change the entity type
          // to 'product_display'. This non existent entity type will be changed
          // by the entity_type static map configured in the
          // migration_plugins_alter.
          $row->setSourceProperty('entity_type', 'product_display');
        }
      }
    }
  }

  /**
   * Helper to get the product types from the source database.
   *
   * @param \Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The event.
   *
   * @return array
   *   An array of product type names.
   */
  protected function getProductTypes(MigratePrepareRowEvent $event) {
    if (!empty($this->productTypes)) {
      return $this->productTypes;
    }
    /** @var \Drupal\migrate\Plugin\Migration $migration */
    $migration = $event->getMigration();
    /** @var \Drupal\migrate\Plugin\migrate\source\SqlBase $source_plugin */
    $source_plugin = $migration->getSourcePlugin();
    if (method_exists($source_plugin, 'getDatabase')) {
      $connection = $source_plugin->getDatabase();
      if ($connection->schema()->tableExists('node_type')) {
        $query = $connection->select('commerce_product_type', 'pt')
          ->fields('pt', ['type']);
        $product_node_types = $query->execute()->fetchCol();
        return ($product_node_types);
      }
    }
    return [];
  }

}
