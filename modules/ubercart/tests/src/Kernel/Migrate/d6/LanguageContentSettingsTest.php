<?php

namespace Drupal\Tests\commerce_migrate_ubercart\Kernel\Migrate\d6;

use Drupal\language\Entity\ContentLanguageSettings;

/**
 * Tests migration of language content setting variables.
 *
 * The variables are language_content_type_$type, i18n_node_options_* and
 * i18n_lock_node_*.
 *
 * @requires migrate_plus
 *
 * @group commerce_migrate
 * @group commerce_migrate_ubercart_d6
 */
class MigrateLanguageContentSettingsTest extends Ubercart6TestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules =
    [
      'content_translation',
      'language',
      'menu_ui',
      'path',
      'commerce_product',
      'migrate_plus',
    ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('commerce_product');
    $this->installConfig(['node']);
    $this->installConfig(['commerce_product']);
    $this->executeMigrations([
      'd6_node_type',
      'd6_ubercart_product_type',
      'd6_language_content_settings',
      'd6_ubercart_language_content_settings',
    ]);
  }

  /**
   * Tests migration of content language settings.
   */
  public function testLanguageContent() {
    // Commerce products.
    $config = ContentLanguageSettings::loadByEntityTypeBundle('commerce_product', 'product');
    $this->assertSame('commerce_product', $config->getTargetEntityTypeId());
    $this->assertSame('product', $config->getTargetBundle());
    $this->assertSame('current_interface', $config->getDefaultLangcode());
    $this->assertTrue($config->getThirdPartySetting('content_translation', 'enabled'));
    $this->assertFalse($config->isDefaultConfiguration());
    $this->assertTrue($config->isLanguageAlterable());

    $config = ContentLanguageSettings::loadByEntityTypeBundle('commerce_product', 'ship');
    $this->assertSame('commerce_product', $config->getTargetEntityTypeId());
    $this->assertSame('ship', $config->getTargetBundle());
    $this->assertSame('site_default', $config->getDefaultLangcode());
    $this->assertNull($config->getThirdPartySetting('content_translation', 'enabled'));
    $this->assertTrue($config->isDefaultConfiguration());
    $this->assertFalse($config->isLanguageAlterable());

    $config = ContentLanguageSettings::loadByEntityTypeBundle('commerce_product', 'default');
    $this->assertSame('commerce_product', $config->getTargetEntityTypeId());
    $this->assertSame('default', $config->getTargetBundle());
    $this->assertSame('site_default', $config->getDefaultLangcode());
    $this->assertNull($config->getThirdPartySetting('content_translation', 'enabled'));
    $this->assertTrue($config->isDefaultConfiguration());
    $this->assertFalse($config->isLanguageAlterable());

    // Node types.
    $config = ContentLanguageSettings::loadByEntityTypeBundle('node', 'page');
    $this->assertSame('node', $config->getTargetEntityTypeId());
    $this->assertSame('page', $config->getTargetBundle());
    $this->assertSame('site_default', $config->getDefaultLangcode());
    $this->assertNull($config->getThirdPartySetting('content_translation', 'enabled'));
    $this->assertTrue($config->isDefaultConfiguration());
    $this->assertFalse($config->isLanguageAlterable());

    $config = ContentLanguageSettings::loadByEntityTypeBundle('node', 'product');
    $this->assertSame('node', $config->getTargetEntityTypeId());
    $this->assertSame('product', $config->getTargetBundle());
    $this->assertSame('site_default', $config->getDefaultLangcode());
    $this->assertNull($config->getThirdPartySetting('content_translation', 'enabled'));
    $this->assertTrue($config->isDefaultConfiguration());
    $this->assertFalse($config->isLanguageAlterable());

    $config = ContentLanguageSettings::loadByEntityTypeBundle('node', 'product_kit');
    $this->assertSame('node', $config->getTargetEntityTypeId());
    $this->assertSame('product_kit', $config->getTargetBundle());
    $this->assertSame('site_default', $config->getDefaultLangcode());
    $this->assertNull($config->getThirdPartySetting('content_translation', 'enabled'));
    $this->assertTrue($config->isDefaultConfiguration());
    $this->assertFalse($config->isLanguageAlterable());

    $config = ContentLanguageSettings::loadByEntityTypeBundle('node', 'ship');
    $this->assertSame('node', $config->getTargetEntityTypeId());
    $this->assertSame('ship', $config->getTargetBundle());
    $this->assertSame('site_default', $config->getDefaultLangcode());
    $this->assertNull($config->getThirdPartySetting('content_translation', 'enabled'));
    $this->assertTrue($config->isDefaultConfiguration());
    $this->assertFalse($config->isLanguageAlterable());
  }

}
