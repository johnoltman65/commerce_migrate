<?php

namespace Drupal\commerce_migrate_ubercart\Plugin\migrate\destination\d6;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_price\Calculator;
use Drupal\migrate\Row;
use Drupal\commerce_price\Price;
use Drupal\migrate\Plugin\migrate\destination\EntityContentBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\migrate\MigrateSkipRowException;

/**
 * Commerce payment destination for Ubercart 6.
 *
 * @MigrateDestination(
 *   id = "entity:commerce_payment"
 * )
 */
class CommercePayment extends EntityContentBase {

  /**
   * The entity type manager, used to fetch entity link templates.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Builds a payment entity destination.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The storage for this entity type.
   * @param array $bundles
   *   The list of bundles this entity type has.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_manager
   *   The field type plugin manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager, used to fetch entity link templates.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, EntityStorageInterface $storage, array $bundles, EntityManagerInterface $entity_manager, FieldTypePluginManagerInterface $field_type_manager, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $storage, $bundles, $entity_manager, $field_type_manager);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    $entity_type = static::getEntityTypeId($plugin_id);
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('entity.manager')->getStorage($entity_type),
      array_keys($container->get('entity.manager')
        ->getBundleInfo($entity_type)),
      $container->get('entity.manager'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('entity_type.manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $amount = $row->getDestinationProperty('amount/number');
    if ($amount >= 0) {
      // Not a refund, nothing to do here.
      return parent::import($row, $old_destination_id_values);
    }
    else {
      // This is a refund and it needs to be attached to a commerce payment.
      // Search all existing payments for this order to find a suitable payment
      // or payments to add the refund to. The refund may be spread across more
      // than one payment.
      // Get all the existing payments for this order from the destination.
      $order_id = $row->getSourceProperty('order_id');
      $query = $this->entityTypeManager->getStorage('commerce_payment')->getQuery();
      $ids = $query
        ->condition('order_id', $order_id)
        ->execute();
      $payments = $this->entityTypeManager->getStorage('commerce_payment')->loadMultiple($ids);

      $current_refund = strval(abs($amount));
      foreach ($payments as $payment) {
        // Loop through all payments adding the current refund amount, or a
        // portion thereof, to the current payment. The refund amount is not to
        // be more than the payment amount.
        $paid_amount = $payment->getAmount()->getNumber();
        if ($paid_amount > 0) {
          // Only add refunds to payments with a positive payment amount.
          $refund_number = $payment->getRefundedAmount()->getNumber();
          $refund_currency_code = $payment->getRefundedAmount()->getCurrencyCode();
          $total_refund_amount = Calculator::add($refund_number, $current_refund);
          $diff = Calculator::subtract($paid_amount, $total_refund_amount);
          if ($diff < 0) {
            // The paid amount does not cover any existing refund plus the
            // current refund.
            // Set the refund amount to the paid amount of the payment.
            $new_refund_amount = new Price($paid_amount, $refund_currency_code);
            $state = 'refunded';
          }
          else {
            // The total current refund amount can be attached to this payment.
            /** @var \Drupal\commerce_price\Price $new_refund_amount */
            $new_refund_amount = new Price(strval(abs($total_refund_amount)), $refund_currency_code);
            $state = (Calculator::subtract($paid_amount, $total_refund_amount) == 0) ? 'refunded' : 'partially_refunded';
          }
          // Update this payment and save.
          $payment->setRefundedAmount($new_refund_amount);
          $payment->setState($state);
          $payment->save();
          // Update the current refund amount.
          $current_refund = Calculator::subtract($current_refund, Calculator::subtract($new_refund_amount->getNumber(), $refund_number));
          if ($current_refund == 0) {
            break;
          }
        }
      }
      if ($current_refund != 0) {
        throw new MigrateSkipRowException('Refund exceeds payments');
      }
      throw new MigrateSkipRowException('Refund row');
    }
  }

}
