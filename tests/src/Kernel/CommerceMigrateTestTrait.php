<?php

namespace Drupal\Tests\commerce_migrate\Kernel;

use Drupal\address\AddressInterface;
use Drupal\address\Plugin\Field\FieldType\AddressItem;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_price\Entity\Currency;
use Drupal\commerce_price\Entity\CurrencyInterface;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_store\Entity\Store;
use Drupal\profile\Entity\Profile;

/**
 * Helper function to test migrations.
 */
trait CommerceMigrateTestTrait {

  /**
   * Asserts an address field.
   *
   * @param \Drupal\address\AddressInterface $address
   *   The address id.
   * @param string $expected_country_code
   *   The country code.
   * @param string $expected_administrative_area
   *   The administrative area.
   * @param string $expected_locality
   *   The locality.
   * @param string $expected_dependent_locality
   *   The dependent locality.
   * @param string $expected_postal_code
   *   The postal code.
   * @param string $expected_sorting_code
   *   The sorting code.
   * @param string $expected_address_line_1
   *   Address line 1.
   * @param string $expected_address_line_2
   *   Address line 2.
   * @param string $expected_given_name
   *   The given name.
   * @param string $expected_additional_name
   *   Any additional names.
   * @param string $expected_family_name
   *   The family name.
   * @param string $expected_organization
   *   The organization string.
   */
  public function assertAddressItem(AddressInterface $address, $expected_country_code, $expected_administrative_area, $expected_locality, $expected_dependent_locality, $expected_postal_code, $expected_sorting_code, $expected_address_line_1, $expected_address_line_2, $expected_given_name, $expected_additional_name, $expected_family_name, $expected_organization) {
    $this->assertInstanceOf(AddressItem::class, $address);
    $this->assertSame($expected_country_code, $address->getCountryCode());
    $this->assertSame($expected_administrative_area, $address->getAdministrativeArea());
    $this->assertSame($expected_locality, $address->getLocality());
    $this->assertSame($expected_dependent_locality, $address->getDependentLocality());
    $this->assertSame($expected_postal_code, $address->getPostalCode());
    $this->assertSame($expected_sorting_code, $address->getSortingCode());
    $this->assertSame($expected_address_line_1, $address->getAddressLine1());
    $this->assertSame($expected_address_line_2, $address->getAddressLine2());
    $this->assertSame($expected_given_name, $address->getGivenName());
    $this->assertSame($expected_additional_name, $address->getAdditionalName());
    $this->assertSame($expected_family_name, $address->getFamilyName());
    $this->assertSame($expected_organization, $address->getOrganization());
  }

  /**
   * Asserts a billing profile entity.
   *
   * @param int $id
   *   The profile id.
   * @param string $expected_is_active
   *   The active state of the profile.
   * @param string $expected_created_time
   *   The time the profile was created..
   * @param string $expected_changed_time
   *   The time the profile was last changed.
   */
  public function assertBillingProfile($id, $expected_is_active, $expected_created_time, $expected_changed_time) {
    $profile = Profile::load($id);
    $this->assertNotNull($profile);
    // Billing profiles are always 'customer' bundle.
    $this->assertSame('customer', $profile->bundle());
    $this->assertSame($expected_is_active, $profile->isActive());
    $this->assertSame($expected_created_time, ($profile->getCreatedTime()));
    $this->assertSame($expected_changed_time, $profile->getChangedTime());
  }

  /**
   * Asserts a Currency entity.
   *
   * @param int $id
   *   The currency id.
   * @param string $expected_currency_code
   *   The currency code.
   * @param string $expected_name
   *   The name of the currency.
   * @param string $expected_numeric_code
   *   The numeric code for the currency.
   * @param string $expected_fraction_digits
   *   The number of fraction digits for this currency.
   * @param string $expected_symbol
   *   The currency symbol.
   */
  public function assertCurrencyEntity($id, $expected_currency_code, $expected_name, $expected_numeric_code, $expected_fraction_digits, $expected_symbol) {
    /** @var \Drupal\commerce_price\Entity\CurrencyInterface $currency */
    $currency = Currency::load($id);
    $this->assertInstanceOf(CurrencyInterface::class, $currency);
    $this->assertSame($expected_currency_code, $currency->getCurrencyCode());
    $this->assertSame($expected_name, $currency->getName());
    $this->assertSame($expected_fraction_digits, $currency->getFractionDigits());
    $this->assertSame($expected_numeric_code, $currency->getNumericCode());
    $this->assertSame($expected_symbol, $currency->getSymbol());
  }

  /**
   * Asserts an order entity.
   *
   * @param string $id
   *   The order id.
   * @param string $expected_store_id
   *   The store id.
   * @param string $expected_created_time
   *   The time the order was created.
   * @param string $expected_changed_time
   *   The time the order was changed.
   * @param string $expected_email
   *   The email address for this order.
   * @param string $expected_label
   *   The label for this order.
   */
  public function assertOrder($id, $expected_store_id, $expected_created_time, $expected_changed_time, $expected_email, $expected_label) {
    $order = Order::load($id);
    $this->assertInstanceOf(Order::class, $order);
    $this->assertSame($expected_store_id, $order->getStoreId());
    $this->assertSame($expected_created_time, $order->getCreatedTime());
    $this->assertSame($expected_changed_time, $order->getChangedTime());
    $this->assertSame($expected_email, $order->getEmail());
    $this->assertSame($expected_label, $order->getState()->getLabel());
    $this->assertNotNull($order->getBillingProfile());
  }

  /**
   * Asserts an order item.
   *
   * @param int $id
   *   The order item id.
   * @param string $expected_quantity
   *   The order quantity.
   * @param string $expected_title
   *   The title of the item.
   */
  public function assertOrderItem($id, $expected_quantity, $expected_title) {
    $order_item = OrderItem::load($id);
    $this->assertInstanceOf(OrderItem::class, $order_item);
    $this->assertSame($expected_quantity, $order_item->getQuantity());
    $this->assertEquals($expected_title, $order_item->getTitle());
  }

  /**
   * Asserts a product.
   *
   * @param int $id
   *   The product id.
   * @param string $expected_title
   *   The title of the product.
   * @param string $expected_is_published
   *   The published status of the product.
   * @param string $expected_changed_time
   *   The time the product was last changed.
   * @param array $expected_store_ids
   *   The ids of the stores for this product.
   */
  public function assertProductEntity($id, $expected_title, $expected_is_published, $expected_changed_time, array $expected_store_ids) {
    $product = Product::load($id);
    $this->assertInstanceOf(Product::class, $product);
    $this->assertSame($expected_title, $product->getTitle());
    $this->assertSame($expected_is_published, $product->isPublished());
    $this->assertSame($expected_changed_time, $product->getChangedTime());
    $this->assertSame($expected_store_ids, $product->getStoreIds());
  }

  /**
   * Asserts a product variation.
   *
   * @param int $id
   *   The product variation id.
   * @param string $expected_sku
   *   The SKU.
   * @param string $expected_price_number
   *   The price.
   * @param string $expected_price_currency_code
   *   The currency code.
   */
  public function assertProductVariationEntity($id, $expected_sku, $expected_price_number, $expected_price_currency_code) {
    $variation = ProductVariation::load($id);
    $this->assertInstanceOf(ProductVariation::class, $variation);
    $this->assertSame($expected_sku, 'towel-bath-001', $variation->getSku());
    $this->assertSame($expected_price_number, $variation->getPrice()
      ->getNumber());
    $this->assertSame($expected_price_currency_code, $variation->getPrice()
      ->getCurrencyCode());
  }

  /**
   * Asserts a store entity.
   *
   * @param int $id
   *   The store id.
   * @param string $expected_name
   *   The name of the store.
   * @param string $expected_email
   *   The email address of the store.
   * @param string $expected_default_currency_code
   *   The default currency code of the store.
   * @param string $expected_bundle
   *   The bundle.
   * @param string $expected_owner_id
   *   The owner id.
   */
  public function assertStoreEntity($id, $expected_name, $expected_email, $expected_default_currency_code, $expected_bundle, $expected_owner_id) {
    $store = Store::load($id);
    $this->assertNotNull($store);
    $this->assertInstanceOf(Store::class, $store);
    $this->assertSame($expected_name, $store->getName());
    $this->assertSame($expected_email, $store->getEmail());
    $this->assertSame($expected_default_currency_code, $store->getDefaultCurrencyCode());
    $this->assertSame($expected_bundle, $store->bundle());
    $this->assertSame($expected_owner_id, $store->getOwnerId());
  }

}
