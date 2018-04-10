<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate;

use Drupal\migrate_drupal\Plugin\migrate\FieldMigration;

/**
 * Plugin class for Drupal 7 order migrations dealing with fields and profiles.
 */
class Order extends FieldMigration {

  /**
   * {@inheritdoc}
   */
  public function getProcess() {
    if (!$this->init) {
      $this->init = TRUE;
      $definition['source'] = [
        'entity_type' => 'commerce_order',
        'ignore_map' => TRUE,
      ] + $this->source;
      $definition['destination']['plugin'] = 'null';
      $definition['idMap']['plugin'] = 'null';
      if (\Drupal::moduleHandler()->moduleExists('field')) {
        $definition['source']['plugin'] = 'd7_field_instance';
        $field_migration = $this->migrationPluginManager->createStubMigration($definition);
        foreach ($field_migration->getSourcePlugin() as $row) {
          $field_name = $row->getSourceProperty('field_name');
          $field_type = $row->getSourceProperty('type');
          if (empty($field_type)) {
            continue;
          }
          if ($this->fieldPluginManager->hasDefinition($field_type)) {
            if (!isset($this->fieldPluginCache[$field_type])) {
              $this->fieldPluginCache[$field_type] = $this->fieldPluginManager->createInstance($field_type, [], $this);
            }
            $info = $row->getSource();
            $this->fieldPluginCache[$field_type]
              ->processFieldValues($this, $field_name, $info);
          }
          else {
            $this->process[$field_name] = $field_type;
          }
        }
      }
    }
    return parent::getProcess();
  }

}
