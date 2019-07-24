<?php

namespace Drupal\Tests\commerce_migrate_commerce\Kernel\Migrate\commerce1;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;
use Drupal\Tests\commerce_migrate\Kernel\CommerceMigrateTestTrait;

/**
 * Tests product variation migration.
 *
 * @group commerce_migrate
 * @group commerce_migrate_commerce1
 */
class ProductVariationTest extends Commerce1TestBase {

  use CommerceMigrateTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'comment',
    'commerce_price',
    'commerce_product',
    'commerce_store',
    'datetime',
    'file',
    'image',
    'link',
    'menu_ui',
    'migrate_plus',
    'node',
    'path',
    'profile',
    'system',
    'taxonomy',
    'telephone',
    'text',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('commerce_product');
    $this->installEntitySchema('commerce_product_attribute');
    $this->installEntitySchema('commerce_product_attribute_value');
    $this->installEntitySchema('commerce_product_variation');
    $this->installSchema('comment', ['comment_entity_statistics']);
    $this->installEntitySchema('file');
    $this->installEntitySchema('node');
    $this->installEntitySchema('profile');
    $this->installEntitySchema('taxonomy_term');

    $this->installConfig(['node']);
    $this->installConfig(['commerce_product']);

    // Setup files needed for the taxonomy_term:collection migration.
    $this->installSchema('file', ['file_usage']);
    $this->installEntitySchema('file');
    $this->container->get('stream_wrapper_manager')
      ->registerWrapper('public', PublicStream::class, StreamWrapperInterface::NORMAL);
    $fs = \Drupal::service('file_system');
    // The public file directory active during the test will serve as the
    // root of the fictional Drupal 7 site we're migrating.
    $fs->mkdir('public://sites/default/files', NULL, TRUE);

    $file_paths = [
      'tote-1v1.jpg',
      'tote-1v2.jpg',
      'tote-1v3.jpg',
      'messenger-1v1.jpg',
      'messenger-1v2.jpg',
      'messenger-1v3.jpg',
      'messenger-1v4.jpg',
      'laptopbag1v1.jpg',
      'laptopbag1v2.jpg',
      'laptopbag1v3.jpg',
      'iphone-case-1v1.jpg',
      'iphone-case-1v2.jpg',
      'iphone-case-1v3.jpg',
      'collection-banner-to_wear.jpg',
      'collection-banner-to_carry.jpg',
      'collection-banner-to_drink_with.jpg',
      'collection-banner-to_geek_out.jpg',
      'mug-1v1.jpg',
      'mug-1v2.jpg',
      'mug-1v3.jpg',
      'mug-2v1.jpg',
      'mug-2v2.jpg',
      'mug-2v3.jpg',
      'travel-mug-1v1.jpg',
      'travel-mug-1v2.jpg',
      'travel-mug-1v3.jpg',
      'water-bottle-1v1.jpg',
      'water-bottle-1v2.jpg',
      'water-bottle-1v3.jpg',
      'hat1_v1.jpg',
      'hat1_v2.jpg',
      'hat1_v3.jpg',
      'hat-2v1.jpg',
      'hat-2v2.jpg',
      'hat-2v3.jpg',
      'flip-flop-2v1.jpg',
      'flip-flop-2v2.jpg',
      'flip-flop-2v3.jpg',
      'shoe2_v1.jpg',
      'shoe2_v2.jpg',
      'shoe2_v3.jpg',
      'usb-1v1.jpg',
      'usb-1v2.jpg',
      'usb-1v3.jpg',
      'mens-shirt-1v1.jpg',
      'mens-shirt-1v2.jpg',
      'mens-shirt-1v3.jpg',
      'mens-shirt-2v1b.jpg',
      'mens-shirt-2v2b.jpg',
      'mens-shirt-2v3b.jpg',
      'mens-shirt-2v1.jpg',
      'mens-shirt-2v2.jpg',
      'mens-shirt-2v3.jpg',
      'mens-shirt-2v1p.jpg',
      'mens-shirt-2v2p.jpg',
      'mens-shirt-2v3p.jpg',
      'mens-shirt_3v1cr.jpg',
      'mens-shirt_3v2cr.jpg',
      'mens-shirt_3v3cr.jpg',
      'mens-shirt_3v1.jpg',
      'mens-shirt_3v2.jpg',
      'mens-shirt_3v3.jpg',
      'mens-shirt_3v1b.jpg',
      'mens-shirt_3v2b.jpg',
      'mens-shirt_3v3b.jpg',
      'mens-4v1.jpg',
      'mens-4v2.jpg',
      'mens-4v3.jpg',
      'womens-1v1g.jpg',
      'womens-1v2g.jpg',
      'womens-1v3g.jpg',
      'womens-1v1.jpg',
      'womens-1v2.jpg',
      'womens-1v3.jpg',
      'womens-1v1y.jpg',
      'womens-1v2y.jpg',
      'womens-1v3y.jpg',
      'womens2v1b.jpg',
      'womens2v2b.jpg',
      'womens2v3b.jpg',
      'womens2v1.jpg',
      'womens2v2.jpg',
      'womens2v3.jpg',
      'womens2v1p.jpg',
      'womens2v2p.jpg',
      'womens2v3p.jpg',
      'Sweatshirt-1v1p.jpg',
      'Sweatshirt-1v2p.jpg',
      'Sweatshirt-1v3p.jpg',
      'Sweatshirt-1v1.jpg',
      'Sweatshirt-1v2.jpg',
      'Sweatshirt-1v3.jpg',
      'sweatshirt-2v1b.jpg',
      'Sweatshirt-2v2b.jpg',
      'sweatshirt-2v3b.jpg',
      'sweatshirt-2v1.jpg',
      'Sweatshirt-2v2.jpg',
      'sweatshirt-2v3.jpg',
      'getting-thirsty.jpg',
      'go-green.jpg',
      'social_logins.png',
      'cmt_commerce_customizable_products.png',
      'slideshow-1.jpg',
      'slideshow-2.jpg',
      'slideshow-3.jpg',
    ];
    foreach ($file_paths as $file_path) {
      $filename = 'public://sites/default/files/' . $file_path;
      file_put_contents($filename, str_repeat('*', 8));
    }
    /** @var \Drupal\migrate\Plugin\Migration $migration */
    $migration = $this->getMigration('d7_file');
    // Set the source plugin's source_base_path configuration value, which
    // would normally be set by the user running the migration.
    $source = $migration->getSourceConfiguration();
    $source['constants']['source_base_path'] = $fs->realpath('public://');
    $migration->set('source', $source);
    $this->executeMigration($migration);

    $this->migrateFields();
    $this->executeMigrations([
      'commerce1_product_variation_type',
      'commerce1_product_type',
      'commerce1_product_attribute',
      'd7_taxonomy_term',
    ]);
    $this->migrateProductVariations();
  }

  /**
   * Test product variation migration from Drupal 7 Commerce to Drupal 8.
   */
  public function testProductVariation() {
    $variation = [
      'id' => 1,
      'type' => 'bags_cases',
      'uid' => '1',
      'sku' => 'TOT1-GRN-OS',
      'price' => '16.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'title' => 'Tote Bag 1',
      'order_item_type_id' => 'product',
      'created_time' => '1493287314',
      'changed_time' => '1493287350',
      'attributes' => NULL,
    ];
    $this->assertProductVariationEntity($variation);
    $variation = [
      'id' => 11,
      'type' => 'hats',
      'uid' => '1',
      'sku' => 'HAT1-GRY-OS',
      'price' => '16.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'title' => 'Hat 1',
      'order_item_type_id' => 'product',
      'created_time' => '1493287364',
      'changed_time' => '1493287400',
      'attributes' => NULL,
    ];
    $this->assertProductVariationEntity($variation);
    $variation = [
      'id' => 12,
      'type' => 'hats',
      'uid' => '1',
      'sku' => 'HAT2-BLK-OS',
      'price' => '12.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'title' => 'Hat 2',
      'order_item_type_id' => 'product',
      'created_time' => '1493287369',
      'changed_time' => '1493287405',
      'attributes' => NULL,
    ];
    $this->assertProductVariationEntity($variation);
    $variation = [
      'id' => 12,
      'type' => 'hats',
      'uid' => '1',
      'sku' => 'HAT2-BLK-OS',
      'price' => '12.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'title' => 'Hat 2',
      'order_item_type_id' => 'product',
      'created_time' => '1493287369',
      'changed_time' => '1493287405',
      'attributes' => NULL,
    ];
    $this->assertProductVariationEntity($variation);
    $variation = [
      'id' => 19,
      'type' => 'shoes',
      'uid' => '1',
      'sku' => 'SHO2-PRL-04',
      'price' => '40.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'title' => 'Shoe 2',
      'order_item_type_id' => 'product',
      'created_time' => '1493287404',
      'changed_time' => '1493287440',
      'attributes' => NULL,
    ];
    $this->assertProductVariationEntity($variation);
    $variation = [
      'id' => 20,
      'type' => 'shoes',
      'uid' => '1',
      'sku' => 'SHO2-PRL-05',
      'price' => '40.000000',
      'currency' => 'USD',
      'product_id' => NULL,
      'title' => 'Shoe 2',
      'order_item_type_id' => 'product',
      'created_time' => '1493287409',
      'changed_time' => '1493287445',
      'attributes' => NULL,
    ];
    $this->assertProductVariationEntity($variation);
    $variation = [
      'id' => 28,
      'type' => 'storage_devices',
      'uid' => '1',
      'sku' => 'USB-BLU-08',
      'price' => '11.990000',
      'currency' => 'USD',
      'product_id' => NULL,
      'title' => 'Storage 1',
      'order_item_type_id' => 'product',
      'created_time' => '1493287449',
      'changed_time' => '1493287485',
      'attributes' => NULL,
    ];
    $this->assertProductVariationEntity($variation);
    $variation = [
      'id' => 29,
      'type' => 'storage_devices',
      'uid' => '1',
      'sku' => 'USB-BLU-16',
      'price' => '17.990000',
      'currency' => 'USD',
      'product_id' => NULL,
      'title' => 'Storage 1',
      'order_item_type_id' => 'product',
      'created_time' => '1493287454',
      'changed_time' => '1493287490',
      'attributes' => NULL,
    ];
    $this->assertProductVariationEntity($variation);
    $variation = [
      'id' => 30,
      'type' => 'storage_devices',
      'uid' => '1',
      'sku' => 'USB-BLU-32',
      'price' => '29.990000',
      'currency' => 'USD',
      'product_id' => NULL,
      'title' => 'Storage 1',
      'order_item_type_id' => 'product',
      'created_time' => '1493287459',
      'changed_time' => '1493287495',
      'attributes' => NULL,
    ];
    $this->assertProductVariationEntity($variation);
  }

}
