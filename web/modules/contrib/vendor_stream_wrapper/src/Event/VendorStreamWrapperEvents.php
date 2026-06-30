<?php

namespace Drupal\vendor_stream_wrapper\Event;

/**
 * Defines the Vendor Stream Wrapper events.
 */
final class VendorStreamWrapperEvents {

  /**
   * The name of the event fired to collect allowed file name patterns.
   *
   * This event allows modules to provide safe-list patterns, which are used to
   * determine if a file located in the vendor directory should be publicly
   * available. The event listener method receives a
   * \Drupal\vendor_stream_wrapper\Event\VendorStreamWrapperCollectSafeListPatternsEvent
   * instance.
   *
   * @Event
   *
   * @see \Drupal\vendor_stream_wrapper\Service\VendorStreamWrapperManager::getSafeListRegexPatterns()
   * @see \Drupal\vendor_stream_wrapper\EventSubscriber\VendorStreamWrapperEventSubscriber
   *
   * @var string
   */
  public const COLLECT_SAFE_LIST_REGEX_PATTERNS = 'vendor_stream_wrapper.collect_safe_list_regex_patterns';

}
