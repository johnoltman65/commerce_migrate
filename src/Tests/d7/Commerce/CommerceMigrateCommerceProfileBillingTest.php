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
    'address',
    'entity',
    'inline_entity_form',
    'profile',
    'state_machine',
    'commerce_price',
    'commerce_store',
    'commerce_order',
    'views',
    'commerce_migrate',
  ];

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig(static::$modules);
    $this->installEntitySchema('view');
    $this->installEntitySchema('profile');

    $this->executeMigrations([
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
