<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\uc6;

use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests customer profile type migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_uc6
 */
class ProfileTypeCustomerTest extends Ubercart6TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['profile'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('profile');
    $this->executeMigration('uc6_profile_type');
  }

  /**
   * Test profile migration.
   */
  public function testProfileType() {
    $this->assertProfileType('customer', 'Customer', TRUE, TRUE);
  }

}
