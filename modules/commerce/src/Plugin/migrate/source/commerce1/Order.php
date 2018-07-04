<?php

namespace Drupal\commerce_migrate_commerce\Plugin\migrate\source\commerce1;

use CommerceGuys\Intl\Currency\CurrencyRepository;
use Drupal\commerce_store\Resolver\DefaultStoreResolver;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\MigrateException;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Gets Commerce 1 commerce_order data from database.
 *
 * @MigrateSource(
 *   id = "commerce1_order",
 *   source_module = "commerce_order"
 * )
 */
class Order extends FieldableEntity {

  /**
   * The default store resolver.
   *
   * @var \Drupal\commerce_store\Resolver\DefaultStoreResolver
   */
  protected $defaultStoreResolver;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityManagerInterface $entity_manager, DefaultStoreResolver $default_store_resolver) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state, $entity_manager);
    $this->defaultStoreResolver = $default_store_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('state'),
      $container->get('entity.manager'),
      $container->get('commerce_store.default_store_resolver')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'order_id' => t('Order ID'),
      'order_number' => t('Order Number'),
      'revision_id' => t('Revision ID'),
      'type' => t('Type'),
      'uid' => t('User ID'),
      'mail' => t('Email'),
      'status' => t('Status'),
      'created' => t('Created'),
      'changed' => t('Changed'),
      'hostname' => t('Hostname'),
      'data' => t('Data'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['order_id']['type'] = 'integer';
    $ids['order_id']['alias'] = 'ord';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('commerce_order', 'ord')
      ->fields('ord', array_keys($this->fields()));

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $default_store = $this->defaultStoreResolver->resolve();
    if ($default_store) {
      $row->setDestinationProperty('store_id', $default_store->id());
    }
    else {
      throw new MigrateException('You must have a store saved in order to import orders.');
    }

    // Get Field API field values.
    $nid = $row->getSourceProperty('order_id');
    $vid = $row->getSourceProperty('revision_id');
    foreach (array_keys($this->getFields('commerce_order', $row->getSourceProperty('type'))) as $field) {
      $row->setSourceProperty($field, $this->getFieldValues('commerce_order', $field, $nid, $vid));
    }

    // Include the number of currency fraction digits in the price.
    $currencyRepository = new CurrencyRepository();
    $value = $row->getSourceProperty('commerce_order_total');
    $currency_code = $value[0]['currency_code'];
    $value[0]['fraction_digits'] = $currencyRepository->get($currency_code)->getFractionDigits();
    $row->setSourceProperty('commerce_order_total', $value);

    $data = unserialize($row->getSourceProperty('data'));
    $row->setSourceProperty('data', $data);
    return parent::prepareRow($row);
  }

}
