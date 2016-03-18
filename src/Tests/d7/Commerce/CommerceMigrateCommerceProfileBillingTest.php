<?php

namespace Drupal\commerce_migrate\Tests\d7\Commerce;

use Drupal\profile\Entity\Profile;

/**
 * Tests billing profile migration.
 *
 * @group commerce_migrate
 */
class CommerceMigrateCommerceProfileBillingTest extends CommerceMigrateCommerce1TestBase {
  static $modules = [
    'profile',
    'commerce_order',
  ];

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('profile');
    $this->installConfig(static::$modules);

    $this->executeMigrations([
      'd7_field',
      'd7_field_instance',
      'd7_billing_profile',
    ]);
  }

  /**
   * Test profile migration from Drupal 7 to 8.
   */
  public function testProfile() {
    /** @var $profile */
    $profile = Profile::load(1);
    $this->assertEqual($profile->getType(), 'billing');
    $this->assertEqual($profile->isActive(), TRUE);
    $this->assertEqual($profile->getCreatedTime(), 1458216500);
    $this->assertEqual($profile->getChangedTime(), 1458216500);
  }
}
