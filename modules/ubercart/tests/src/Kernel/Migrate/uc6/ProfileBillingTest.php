<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc6;

use Drupal\profile\Entity\Profile;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests billing profile migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc6
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
      'uc6_billing_profile',
    ]);
  }

  /**
   * Test profile migration.
   */
  public function testProfileBilling() {
    $this->assertBillingProfile(1, '3', TRUE, '1492868907', '1523578137');
    $profile = Profile::load(1);
    $address = $profile->get('address')->first()->getValue();
    $this->assertAddressField($address, 'US', NULL, '', NULL, '', NULL, '', '', '', NULL, '', '');
    $phone = $profile->get('phone')->getValue();
    $this->assertSame([], $phone);

    $this->assertBillingProfile(2, '5', TRUE, '1492989920', '1508916762');
    $profile = Profile::load(2);
    $address = $profile->get('address')->first()->getValue();
    $this->assertAddressField($address, 'US', 'US-WY', 'World B', NULL, '7654', NULL, '42 View Lane', 'Frogstar', 'Trin', NULL, 'Tragula', 'Perspective Ltd.');
    $phone = $profile->get('phone')->getValue()[0]['value'];
    $this->assertSame('111-9876', $phone);
  }

}
