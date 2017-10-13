<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\profile\Entity\Profile;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests billing profile migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class ProfileBillingTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'commerce_migrate_ubercart',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('profile');
    $this->installConfig('commerce_order');
    $this->installConfig('commerce_migrate_ubercart');
    $this->executeMigrations([
      'd6_filter_format',
      'd6_user_role',
      'd6_user',
      'd6_ubercart_billing_profile',
    ]);
  }

  /**
   * Test profile migration from Drupal 6 to 8.
   */
  public function testProfileBilling() {
    $this->assertBillingProfile(1, '3', TRUE, '1492868907', '1493078815');
    $profile = Profile::load(1);
    $address = $profile->get('address')->first()->getValue();
    $this->assertAddressField($address, 'US', '', '', NULL, '', NULL, '', '', '', NULL, '', '');
    $phone = $profile->get('phone')->getValue();
    $this->assertSame([], $phone);

    $this->assertBillingProfile(2, '5', TRUE, '1492989920', '1493081092');
    $profile = Profile::load(2);
    $address = $profile->get('address')->first()->getValue();
    $this->assertAddressField($address, 'US', 'US-WY', 'World B', NULL, '7654', NULL, '42 View Lane', 'Frogstar', 'Trin', NULL, 'Tragula', 'Perspective Ltd.');
    $phone = $profile->get('phone')->getValue()[0]['value'];
    $this->assertSame('111-9876', $phone);
  }

  /**
   * Asserts an address field.
   *
   * @param array $address
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
  public function assertAddressField(array $address, $expected_country_code, $expected_administrative_area, $expected_locality, $expected_dependent_locality, $expected_postal_code, $expected_sorting_code, $expected_address_line_1, $expected_address_line_2, $expected_given_name, $expected_additional_name, $expected_family_name, $expected_organization) {
    $this->assertSame($expected_country_code, $address['country_code']);
    $this->assertSame($expected_administrative_area, $address['administrative_area']);
    $this->assertSame($expected_locality, $address['locality']);
    $this->assertSame($expected_dependent_locality, $address['dependent_locality']);
    $this->assertSame($expected_postal_code, $address['postal_code']);
    $this->assertSame($expected_sorting_code, $address['sorting_code']);
    $this->assertSame($expected_address_line_1, $address['address_line1']);
    $this->assertSame($expected_address_line_2, $address['address_line2']);
    $this->assertSame($expected_given_name, $address['given_name']);
    $this->assertSame($expected_additional_name, $address['additional_name']);
    $this->assertSame($expected_family_name, $address['family_name']);
    $this->assertSame($expected_organization, $address['organization']);
  }

}
