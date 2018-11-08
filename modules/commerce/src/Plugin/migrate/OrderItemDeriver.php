<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Database\DatabaseExceptionWrapper;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\migrate\Exception\RequirementsException;
use Drupal\migrate\Plugin\MigrationDeriverTrait;
use Drupal\migrate_drupal\Plugin\MigrateFieldPluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Deriver for Commerce 1 line items based on line item types.
 */
class OrderItemDeriver extends DeriverBase implements ContainerDeriverInterface {

  use MigrationDeriverTrait;

  /**
   * The plugin ID.
   *
   * @var string
   */
  protected $basePluginId;

  /**
   * Already-instantiated field plugins, keyed by ID.
   *
   * @var \Drupal\migrate_drupal\Plugin\MigrateFieldInterface[]
   */
  protected $fieldPluginCache;

  /**
   * The field plugin manager.
   *
   * @var \Drupal\migrate_drupal\Plugin\MigrateFieldPluginManagerInterface
   */
  protected $fieldPluginManager;

  /**
   * D7OrderItemDeriver constructor.
   *
   * @param string $base_plugin_id
   *   The base plugin ID for the plugin ID.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\migrate_drupal\Plugin\MigrateFieldPluginManagerInterface $field_manager
   *   The field plugin manager.
   */
  public function __construct($base_plugin_id, EventDispatcherInterface $event_dispatcher, MigrateFieldPluginManagerInterface $field_manager) {
    $this->basePluginId = $base_plugin_id;
    $this->fieldPluginManager = $field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('event_dispatcher'),
      $container->get('plugin.manager.migrate.field')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // @todo: Convert to new trait. See https://www.drupal.org/node/2951550.
    $order_item_types = static::getSourcePlugin('commerce1_order_item_type');
    try {
      $order_item_types->checkRequirements();
    }
    catch (RequirementsException $e) {
      // If the d7_order_item_type requirements failed, that means we do not
      // have a  Drupal source database configured - there is nothing to
      // generate.
      return parent::getDerivativeDefinitions($base_plugin_definition);
    }

    $fields = [];
    try {
      $source_plugin = static::getSourcePlugin('d7_field_instance');
      $source_plugin->checkRequirements();

      // Read all field instance definitions in the source database.
      foreach ($source_plugin as $row) {
        if ($row->getSourceProperty('entity_type') == 'commerce_line_item') {
          $fields[$row->getSourceProperty('bundle')][$row->getSourceProperty('field_name')] = $row->getSource();
        }
      }
    }
    catch (RequirementsException $e) {
      // If checkRequirements() failed then the field module did not exist and
      // we do not have any fields. Therefore, $fields will be empty and below
      // we'll create a migration just for the line item properties.
    }

    try {
      foreach ($order_item_types as $row) {
        $line_item_type = $row->getSourceProperty('type');
        $values = $base_plugin_definition;

        $values['label'] = t('@label (@type)', [
          '@label' => $values['label'],
          '@type' => $row->getSourceProperty('name'),
        ]);
        $values['source']['line_item_type'] = $line_item_type;
        $values['destination']['default_bundle'] = $line_item_type;

        /** @var \Drupal\migrate\Plugin\migration $migration */
        $migration = \Drupal::service('plugin.manager.migration')->createStubMigration($values);
        if (isset($fields[$line_item_type])) {
          foreach ($fields[$line_item_type] as $field_name => $info) {
            $field_type = $info['type'];
            try {
              $plugin_id = $this->fieldPluginManager->getPluginIdFromFieldType($field_type, ['core' => 7], $migration);
              if (!isset($this->fieldPluginCache[$field_type])) {
                $this->fieldPluginCache[$field_type] = $this->fieldPluginManager->createInstance($plugin_id, ['core' => 7], $migration);
              }
              $this->fieldPluginCache[$field_type]
                ->defineValueProcessPipeline($migration, $field_name, $info);
            }
            catch (PluginNotFoundException $ex) {
              $migration->setProcessOfProperty($field_name, $field_name);
            }
          }
        }
        $this->derivatives[$line_item_type] = $migration->getPluginDefinition();
      }
    }
    catch (DatabaseExceptionWrapper $e) {
      // Once we begin iterating the source plugin it is possible that the
      // source tables will not exist. This can happen when the
      // MigrationPluginManager gathers up the migration definitions but we do
      // not actually have a Drupal 7 source database.
    }
    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
