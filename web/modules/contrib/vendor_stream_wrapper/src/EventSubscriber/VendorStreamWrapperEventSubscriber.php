<?php

namespace Drupal\vendor_stream_wrapper\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\vendor_stream_wrapper\Event\VendorStreamWrapperCollectSafeListRegexPatternsEvent;
use Drupal\vendor_stream_wrapper\Event\VendorStreamWrapperEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Events subscriber.
 */
class VendorStreamWrapperEventSubscriber implements EventSubscriberInterface {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a VendorStreamWrapperEventSubscriber object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      VendorStreamWrapperEvents::COLLECT_SAFE_LIST_REGEX_PATTERNS => 'setAllowedPatternsFromConfig',
    ];
  }

  /**
   * Sets the allowed download patterns as stored in the configuration.
   *
   * @param \Drupal\vendor_stream_wrapper\Event\VendorStreamWrapperCollectSafeListRegexPatternsEvent $event
   *   The event object storing the patterns for files/directories of the vendor
   *   directory that should be publicly accessible.
   */
  public function setAllowedPatternsFromConfig(VendorStreamWrapperCollectSafeListRegexPatternsEvent $event): void {
    // Get from configuration the patterns of vendor files that are allowed to
    // be downloaded/accessed.
    $allowed_file_patterns = $this->configFactory->get('vendor_stream_wrapper.settings')->get('allowed_file_patterns') ?? [];

    // Ensure only allowed characters are used in the patterns.
    $allowed_file_patterns = array_filter(preg_replace('/[^a-zA-Z0-9_\/\.\-\*]+/', '', $allowed_file_patterns));

    // Prepare the patterns to be used as regular expressions.
    $allowed_file_patterns = array_map(static function (string $pattern) {
      return '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';
    }, $allowed_file_patterns);

    // Add the patterns to the safe list.
    $event->getVendorStreamWrapperManager()->addSafeListRegexPatterns($allowed_file_patterns);
  }

}
