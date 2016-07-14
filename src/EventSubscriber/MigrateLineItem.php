<?php

namespace Drupal\commerce_migrate\EventSubscriber;

use Drupal\commerce_order\Entity\LineItem;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MigrateLineItem implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::POST_ROW_SAVE][] = 'postRowSave';
    return $events;
  }

  /**
   * Reacts to the POST_ROW_SAVE event.
   *
   * @param \Drupal\migrate\Event\MigratePostRowSaveEvent $event
   *   The event.
   */
  public function postRowSave(MigratePostRowSaveEvent $event) {
    $destination_config = $event->getMigration()->getDestinationConfiguration();

    // If the destination is a line item we need to ensure the reference to
    // the order exists.
    if ($destination_config['plugin'] == 'entity:commerce_line_item') {
      $line_item = LineItem::load($event->getRow()->getDestinationProperty('line_item_id'));
      $order = $line_item->getOrder();
      if ($order) {
        $order->addLineItem($line_item);
        $order->save();
      }
    }
  }

}
