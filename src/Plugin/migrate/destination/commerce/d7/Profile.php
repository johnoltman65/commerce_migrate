<?php

namespace Drupal\commerce_migrate\Plugin\migrate\destination\commerce\d7;

use Drupal\migrate\Plugin\migrate\destination\EntityContentBase;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * @MigrateDestination(
 *   id = "entity:profile"
 * )
 */
class Profile extends EntityContentBase {

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) {
    return 'profile';
  }

  /**
   * {@inheritdoc}
   */
  protected function save(ContentEntityInterface $entity, array $old_destination_id_values = array()) {
    $entity->save();

    return [
      $this->getKey('id') => $entity->id(),
      $this->getKey('revision') => $entity->getRevisionId(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids = parent::getIds();
    $ids[$this->getKey('revision')]['type'] = 'integer';

    return $ids;
  }

}
