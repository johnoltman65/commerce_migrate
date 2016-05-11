<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;

use Drupal\profile\Entity\Profile;

/**
 * Tests billing profile migration.
 *
 * @group commerce_migrate
 */
class CommerceMigrateCommerceProfileBillingTest extends CommerceMigrateCommerce1TestBase {
  static $modules = [
    'text',
  ];

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->executeMigrations([
      'd7_user_role',
      'd7_user',
//      'd7_field',
//      'd7_field_instance',
      'd7_billing_profile',
    ]);
  }

  /**
   * Test profile migration from Drupal 7 to 8.
   */
  public function testProfile() {
    /** @var $profile */
    $profile = Profile::load(1);
    $this->assertEquals($profile->getType(), 'billing');
    $this->assertEquals($profile->isActive(), TRUE);
    $this->assertEquals($profile->getCreatedTime(), 1458216500);
    $this->assertEquals($profile->getChangedTime(), 1458216500);
  }
}
