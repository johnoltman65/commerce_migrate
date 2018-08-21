<?php

namespace Drupal\commerce_migrate_ubercart\EventSubscriber;

use Drupal\commerce_migrate\Utility;
use Drupal\field\Plugin\migrate\source\d6\Field as D6Field;
use Drupal\field\Plugin\migrate\source\d6\FieldInstance;
use Drupal\field\Plugin\migrate\source\d6\FieldInstancePerFormDisplay;
use Drupal\field\Plugin\migrate\source\d6\FieldInstancePerViewMode;
use Drupal\field\Plugin\migrate\source\d7\Field as D7Field;
use Drupal\language\Plugin\migrate\source\d6\LanguageContentSettings;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Event\MigrateEvents;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
use Drupal\node\Plugin\migrate\source\d6\NodeType;
use Drupal\node\Plugin\migrate\source\d6\ViewMode;
use Drupal\taxonomy\Plugin\migrate\source\d6\TermNode;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles migrate_plus prepare row event.
 *
 * @package Drupal\commerce_migrate_ubercart\EventSubscriber
 */
class PrepareRow implements EventSubscriberInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

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
    $events[MigrateEvents::PREPARE_ROW][] = 'onPrepareRow';
    return $events;
  }

  /**
   * Responds to prepare row event.
   *
   * Since products are nodes in Ubercart 6 the field and node migration need
   * extra information so there is no duplication of products as nodes.
   *
   * Node type: Sets property 'product_type'.
   * Field: Set the entity_type.
   * Field instance, Field formatter, Field widget, View mode: Set the entity
   * type.
   *
   * @param \Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The event.
   */
  public function onPrepareRow(MigratePrepareRowEvent $event) {
    $migration = $event->getMigration();
    $row = $event->getRow();
    $source_plugin = $migration->getSourcePlugin();

    if (Utility::classInArray($source_plugin, [NodeType::class, TermNode::class])) {
      // For Node Type migrations, i.e. d6_node_type, set product_type so all
      // product type rows are skipped.
      $node_type = $row->getSourceProperty('type');
      $this->productTypes = $this->getProductTypes($migration);
      $row->setSourceProperty('product_type', TRUE);
      if (in_array($node_type, $this->productTypes)) {
        $row->setSourceProperty('product_type', NULL);
      }
    }

    // The d6_field migration.
    if (Utility::classInArray($source_plugin, [D6Field::class, D7Field::class])) {
      $this->productTypes = $this->getProductTypes($migration);
      // Get all the instances of this field.
      $instances = $this->getFieldInstances($source_plugin, $row);
      // Determine if the field is on both a product type and node, or just one
      // of product type or node.
      $i = 0;
      foreach ($instances as $instance) {
        if (in_array($instance, $this->productTypes)) {
          $i++;
        }
      }
      if ($i > 0) {
        if ($i == count($instances)) {
          // If all bundles for this field are product types, then change the
          // entity type to 'commerce_product'.
          $row->setSourceProperty('entity_type', 'commerce_product');
        }
        else {
          // This field is used on both nodes and products. Set
          // ubercart_entity_type so that field storage is created for the
          // ubercart product.
          $row->setSourceProperty('ubercart_entity_type', 'commerce_product');
          $row->setSourceProperty('entity_type', 'node');
        }
      }
      else {
        // This field is used on just nodes. Set the entity_type to 'node'.
        $row->setSourceProperty('entity_type', 'node');
      }
    }

    if (Utility::classInArray($source_plugin, [
      FieldInstance::class,
      FieldInstancePerViewMode::class,
      FieldInstancePerFormDisplay::class,
      ViewMode::class,
    ], FALSE)) {
      $this->setEntityType($row, $migration, $row->getSourceProperty('type_name'));
    }

    if (is_a($source_plugin, LanguageContentSettings::class)) {
      // There are two language_content_settings migrations, the core one for
      // nodes and one for products. Allow the core one to only save language
      // content settings for nodes and the latter for products.
      $node_type = $row->getSourceProperty('type');
      $this->productTypes = $this->getProductTypes($migration);
      $row->setSourceProperty('product_type', TRUE);
      if (in_array($node_type, $this->productTypes)) {
        $source = $row->getSource();
        $type = $source['constants']['target_type'];
        if ($type == 'node') {
          // This is the core language content settings migration, do not
          // migrate this product type row.
          $row->setSourceProperty('product_type', NULL);
        }
      }
    }
  }

  /**
   * Helper to set the correct entity type in the source row.
   *
   * @param \Drupal\migrate\Row $row
   *   The row object.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration.
   * @param string $type_name
   *   The type name.
   */
  protected function setEntityType(Row $row, MigrationInterface $migration, $type_name) {
    if ($this->productTypes == []) {
      $this->productTypes = $this->getProductTypes($migration);
    }
    if (in_array($type_name, $this->productTypes)) {
      $row->setSourceProperty('entity_type', 'commerce_product');
    }
    else {
      $row->setSourceProperty('entity_type', 'node');
    }
  }

  /**
   * Helper to get the product types from the source database.
   *
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration.
   *
   * @return array
   *   The product types.
   */
  protected function getProductTypes(MigrationInterface $migration) {
    if (!empty($this->productTypes)) {
      return $this->productTypes;
    }
    /** @var \Drupal\migrate\Plugin\migrate\source\SqlBase $source_plugin */
    $source_plugin = $migration->getSourcePlugin();
    if (method_exists($source_plugin, 'getDatabase')) {
      $this->connection = $source_plugin->getDatabase();
      if ($this->connection->schema()->tableExists('node_type')) {
        $query = $this->connection->select('node_type', 'nt')
          ->fields('nt', ['type'])
          ->condition('module', 'uc_product%', 'LIKE')
          ->distinct();
        $this->productTypes = [$query->execute()->fetchCol()];
      }
    }
    return reset($this->productTypes);
  }

  /**
   * Gets the field instances for the field in this row.
   *
   * @param string $source_plugin
   *   The source plugin for this migration.
   * @param \Drupal\migrate\Row $row
   *   The current row.
   *
   * @return array
   *   An array of bundle names using this field.
   */
  protected function getFieldInstances($source_plugin, Row $row) {
    $instances = [];
    $field_name = $row->getSourceProperty('field_name');

    if (is_a($source_plugin, D6Field::class)) {
      // Get all the instances of this field.
      $query = $this->connection->select('content_node_field', 'cnf')
        ->fields('cnfi', ['type_name'])
        ->distinct();
      $query->innerJoin('content_node_field_instance', 'cnfi', 'cnfi.field_name = cnf.field_name');
      $query->condition('cnf.field_name', $field_name);
      $instances = $query->execute()->fetchCol();
    }

    if (is_a($source_plugin, D7Field::class)) {
      $field_name = $row->getSourceProperty('field_name');
      // Get all the instances of this field.
      $query = $this->connection->select('field_config_instance', 'fci')
        ->fields('fci', ['bundle'])
        ->condition('fc.active', 1)
        ->condition('fc.storage_active', 1)
        ->condition('fc.deleted', 0)
        ->condition('fci.deleted', 0);
      $query->join('field_config', 'fc', 'fci.field_id = fc.id');
      $query->condition('fci.field_name', $field_name);
      $instances = $query->execute()->fetchCol();
    }
    return $instances;
  }

}
