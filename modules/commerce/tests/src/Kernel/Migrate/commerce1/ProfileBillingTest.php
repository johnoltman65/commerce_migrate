<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\commerce1;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;
use Drupal\profile\Entity\Profile;

/**
 * Tests billing profile migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce1
 */
class ProfileBillingTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'commerce_order',
    'commerce_price',
    'commerce_store',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('profile');
    $this->installConfig('commerce_order');
    // @todo Execute the d7_field and d7_field_instance migrations?
    $this->executeMigrations([
      'd7_user_role',
      'd7_user',
      'commerce1_billing_profile',
    ]);
  }

  /**
   * Test profile migration from Drupal 7 Commerce to Drupal 8.
   */
  public function testProfileBilling() {
    $this->assertBillingProfile(1, '4', TRUE, '1493287440', '1493287445');

    $profile = Profile::load(1);
    $this->assertTrue($profile->isDefault());
    $address = $profile->get('address')->first()->getValue();
    $this->assertAddressField($address, 'US', 'CA', 'Visalia', NULL, '93277-8329', '', '16 Hampton Ct', NULL, 'Sample', NULL, 'Customer', NULL);

    $profile = Profile::load(4);
    $this->assertFalse($profile->isDefault());
    $address = $profile->get('address')->first()->getValue();
    $this->assertAddressField($address, 'NZ', '', 'Visalia', '', '93277-8329', '', '16 Hampton Ct', '', 'Sample', NULL, 'Customer', NULL);
  }

}
