<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Ubercart;

use Drupal\address\Plugin\Field\FieldType\AddressItem;
use Drupal\commerce_store\Entity\Store;

/**
 * Tests store migration.
 *
 * @group commerce_migrate
 */
class StoreTest extends Ubercart7TestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['commerce_store'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('commerce_store');
    $this->executeMigrations([
      'd7_filter_format',
      'd7_user_role',
      'd7_user',
      'ubercart_currency',
      'd7_ubercart_store'
    ]);
  }

  /**
   * Test store migration from Drupal 7 to 8.
   */
  public function testStore() {
    /** @var \Drupal\commerce_store\Entity\storeInterface $store */
    $store = Store::load(1);
    $this->assertNotNull($store);
    $this->assertInstanceOf(Store::class, $store);
    $this->assertSame("Quark's", $store->getName());
    $this->assertSame('quark@example.com', $store->getEmail());
    $this->assertSame('USD', $store->getDefaultCurrencyCode());
    $this->assertSame('online', $store->bundle());
    $this->assertSame('1', $store->getOwnerId());

    $address = $store->getAddress();
    $this->assertInstanceOf(AddressItem::class, $address);
    $this->assertSame('CA', $address->getCountryCode());
    $this->assertNull($address->getAdministrativeArea());
    $this->assertSame('Deep Space 9', $address->getLocality());
    $this->assertNull($address->getDependentLocality());
    $this->assertSame('9999', $address->getPostalCode());
    $this->assertNull($address->getSortingCode());
    $this->assertSame('47 The Promenade', $address->getAddressLine1());
    $this->assertSame('Lower Level', $address->getAddressLine2());
    // These fields are disabled on the store address.
    $this->assertNull($address->getGivenName());
    $this->assertNull($address->getAdditionalName());
    $this->assertNull($address->getFamilyName());
    $this->assertNull($address->getOrganization());
  }

}
