<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\address\Plugin\Field\FieldType\AddressItem;
use Drupal\commerce_store\Entity\Store;

/**
 * Tests store migration.
 *
 * @group commerce_migrate
 */
class StoreTest extends Ubercart6TestBase {

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
    $this->migrateStore();
  }

  /**
   * Test store migration from Drupal 6 to 8.
   */
  public function testStore() {
    /** @var \Drupal\commerce_store\Entity\storeInterface $store */
    $store = Store::load(1);
    $this->assertNotNull($store);
    $this->assertInstanceOf(Store::class, $store);
    $this->assertSame('Awesome Stuff', $store->getName());
    $this->assertSame('awesome_stuff@example.com', $store->getEmail());
    $this->assertSame('NZD', $store->getDefaultCurrencyCode());
    $this->assertSame('online', $store->bundle());
    $this->assertSame('1', $store->getOwnerId());

    $address = $store->getAddress();
    $this->assertInstanceOf(AddressItem::class, $address);
    $this->assertSame('US', $address->getCountryCode());
    $this->assertNull($address->getAdministrativeArea());
    $this->assertSame('Betelgeuse', $address->getLocality());
    $this->assertNull($address->getDependentLocality());
    $this->assertSame('4242', $address->getPostalCode());
    $this->assertNull($address->getSortingCode());
    $this->assertSame('123 First Street', $address->getAddressLine1());
    $this->assertSame('456 Second Street', $address->getAddressLine2());
    // These fields are disabled on the store address.
    $this->assertNull($address->getGivenName());
    $this->assertNull($address->getAdditionalName());
    $this->assertNull($address->getFamilyName());
    $this->assertNull($address->getOrganization());
  }

}
