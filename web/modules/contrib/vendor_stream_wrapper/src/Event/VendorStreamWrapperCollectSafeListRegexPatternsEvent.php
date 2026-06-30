<?php

namespace Drupal\vendor_stream_wrapper\Event;

use Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Provides a vendor stream wrapper collect safe-list regex pattern event.
 */
class VendorStreamWrapperCollectSafeListRegexPatternsEvent extends Event {

  /**
   * The vendor stream wrapper manager.
   *
   * @var \Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManagerInterface
   */
  protected $vendorStreamWrapperManager;

  /**
   * Constructs the event object.
   *
   * @param Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManagerInterface $vendor_stream_wrapper_manager
   *   The vendor stream wrapper manager.
   */
  public function __construct(VendorStreamWrapperManagerInterface $vendor_stream_wrapper_manager) {
    $this->vendorStreamWrapperManager = $vendor_stream_wrapper_manager;
  }

  /**
   * Returns the vendor stream wrapper manager.
   *
   * @return \Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManagerInterface
   *   The vendor stream wrapper manager.
   */
  public function getVendorStreamWrapperManager(): VendorStreamWrapperManagerInterface {
    return $this->vendorStreamWrapperManager;
  }

}
