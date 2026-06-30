<?php

declare(strict_types=1);

namespace Drupal\Tests\vendor_stream_wrapper\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManager
 * @group vendor_stream_wrapper
 */
class VendorStreamWrapperServiceTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['vendor_stream_wrapper'];

  /**
   * Tests a path output from createUrlFromUri().
   *
   * @covers::createUrlFromUri
   */
  public function testCreateUrlFromUri(): void {
    /** @var \Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManagerInterface $service */
    $service = $this->container->get('vendor_stream_wrapper.manager');

    $path = $service->createUrlFromUri('vendor://acme/anvil/heavy.png');
    $this->assertEquals('/vendor_files/acme/anvil/heavy.png', $path);
  }

}
