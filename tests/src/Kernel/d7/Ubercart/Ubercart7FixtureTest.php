<?php

namespace Drupal\Tests\commerce_migrate\Kernel\d7\Ubercart;
use Drupal\user\Entity\User;

/**
 * Test that ensures fixture can be installed.
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart7
 */
class Ubercart7FixtureTest extends Ubercart7TestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->executeMigrations([
      'd7_user_role',
      'd7_user',
    ]);
  }

  /**
   * If the fixture installed, this will pass.
   */
  public function testItWorked() {
    $user = User::load(11);
    $this->assertEquals('uuphawocheka', $user->getUsername());
  }

}
