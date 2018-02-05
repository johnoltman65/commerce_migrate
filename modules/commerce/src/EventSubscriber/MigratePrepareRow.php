<?php

namespace Drupal\commerce_migrate_commerce\EventSubscriber;

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

    if (is_a($source_plugin, FieldInstance::class)) {
      // If this is node entity then set source property 'commerce1_entity_type'
      // to indicate if this is a product display node or not.
      $row->setSourceProperty('commerce1_entity_type', $row->getSourceProperty('entity_type'));
      if ($row->getSourceProperty('entity_type') === 'node') {
        if (in_array($row->getSourceProperty('bundle'), $this->getProductTypes($event))) {
          $row->setSourceProperty('commerce1_entity_type', 'product_display');

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
