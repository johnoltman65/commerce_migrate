<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d6\Ubercart;

use Drupal\profile\Entity\Profile;

/**
 * Tests billing profile migration.
 *
 * @group commerce_migrate
 */
class ProfileBillingTest extends Ubercart6TestBase {

  static $modules = [
    'text',
  ];

  /**
   * @inheritDoc
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('profile');
    $this->executeMigrations([
      'd6_filter_format',
      'd6_user_role',
      'd6_user',
      'd6_ubercart_billing_profile',
    ]);
  }

  /**
   * Test profile migration from Drupal 7 to 8.
   */
  public function testProfile() {
    /** @var $profile */
    $profile = Profile::load(1);
    $this->assertNotNull($profile);
    $this->assertEquals($profile->getType(), 'billing');
    $this->assertEquals($profile->isActive(), TRUE);
    $this->assertEquals($profile->getCreatedTime(), 1306876624);
    $this->assertEquals($profile->getChangedTime(), 1306876784);
  }

}