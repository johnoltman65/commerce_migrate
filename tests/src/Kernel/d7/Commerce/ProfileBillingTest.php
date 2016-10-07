<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Commerce;

use Drupal\profile\Entity\Profile;

/**
 * Tests billing profile migration.
 *
 * @group commerce_migrate
 */
class ProfileBillingTest extends Commerce1TestBase {

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('profile');
    // @todo Execute the d7_field and d7_field_instance migrations?
    $this->executeMigrations([
      'd7_user_role',
      'd7_user',
      'd7_billing_profile',
    ]);
  }

  /**
   * Test profile migration from Drupal 7 to 8.
   */
  public function testProfile() {
    $profile = Profile::load(1);
    $this->assertNotNull($profile);
    $this->assertEquals($profile->getType(), 'billing');
    $this->assertEquals($profile->isActive(), TRUE);
    $this->assertEquals($profile->getCreatedTime(), 1458216500);
    $this->assertEquals($profile->getChangedTime(), 1458216500);
  }

}
