<?php

declare(strict_types=1);

namespace Drupal\Tests\vendor_stream_wrapper\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Checks vendor_stream_wrapper file download controller.
 *
 * @group vendor_stream_wrapper
 */
class DownloadControllerTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['vendor_stream_wrapper'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->drupalLogin($this->drupalCreateUser([
      'access content',
      'administer site configuration',
    ]));
  }

  /**
   * Tests file download controller.
   */
  public function testDownloadController(): void {
    /** @var \Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManagerInterface $service */
    $service = $this->container->get('vendor_stream_wrapper.manager');
    $url = $service->createUrlFromUri('vendor://jaypan/jquery-colorpicker/images/colorpicker_background.png', FALSE);
    $this->assertEquals('/vendor_files/jaypan/jquery-colorpicker/images/colorpicker_background.png', $url);

    // Try loading the image, should fail without config.
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(403);

    // Now configure the images to be downloadable.
    $this->drupalGet('/admin/config/media/vendor-stream-wrapper');
    $this->submitForm(['allowed_file_patterns' => 'jaypan/jquery-colorpicker/images/*'], 'Save configuration');

    // Try loading the image, should pass.
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
  }

}
