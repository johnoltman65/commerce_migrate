<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\process\commerce1;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigratePluginManagerInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Determines the field name.
 *
 * @code
 * field_name:
 *   plugin: commerce_field_name
 *   source: field_test
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "commerce1_field_name"
 * )
 */
class CommerceFieldName extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The process plugin manager.
   *
   * @var \Drupal\migrate\Plugin\MigratePluginManager
   */
  protected $processPluginManager;

  /**
   * The migration plugin manager.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected $migrationPluginManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationPluginManagerInterface $migration_plugin_manager, MigratePluginManagerInterface $process_plugin_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->migrationPluginManager = $migration_plugin_manager;
    $this->processPluginManager = $process_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.migration'),
      $container->get('plugin.manager.migrate.process')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $field_name = $row->getSourceProperty('field_name');
    // Get the commerce attribute style field name.
    if (($row->getSourceProperty('entity_type') == 'commerce_product') &&
      ($row->getSourceProperty('type') == 'taxonomy_term_reference')) {
      $instances = $row->getSourceProperty('instances');
      // If any instance has a select widget, then this is an attribute.
      foreach ($instances as $instance) {
        $data = unserialize(($instance['data']));
        if ($data['widget']['type'] == 'options_select') {
          // If the row contains the property 'field_name_attribute' then we
          // need to maintain BC and use that as the attribute field name.
          // Otherwise, make a new name without 'field', make sure it is unique
          // and <= 32 characters.
          if ($row->getDestinationProperty('field_name_attribute')) {
            $field_name = $row->getDestinationProperty('field_name_attribute');
            @trigger_error("Migrating attribute field names with a prefix of 'field_' is deprecated. Re configure the d6_field migration.", E_USER_DEPRECATED);
          }
          else {
            $field_name = preg_replace('/^field_/', 'attribute_', $field_name);
            // The field name may be > 32 characters so make it unique.
            $migration = $this->migrationPluginManager->createStubMigration([]);
            $configuration =
              [
                'entity_type' => 'commerce_product_attribute',
                'field' => 'field_name',
                'length' => 29,
              ];
            $plugin = $this->processPluginManager->createInstance('make_unique_entity_field', $configuration, $migration);
            $field_name = $plugin->transform($field_name, $migrate_executable, $row, 'tmp');
          }
          break;
        }
      }
    }
    // For profiles the name of the addressfield changes to address.
    if (($row->getSourceProperty('entity_type') == 'commerce_customer_profile') &&
      ($row->getSourceProperty('type') == 'addressfield')) {
      $field_name = 'address';
    }
    return $field_name;
  }

}
