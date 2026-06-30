<?php

namespace Drupal\vendor_stream_wrapper\Service;

use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\vendor_stream_wrapper\Event\VendorStreamWrapperCollectSafeListRegexPatternsEvent;
use Drupal\vendor_stream_wrapper\Event\VendorStreamWrapperEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

// cspell:ignore creat

/**
 * Service providing management functionality for vendor stream wrappers.
 */
class VendorStreamWrapperManager implements VendorStreamWrapperManagerInterface {

  /**
   * The Stream Wrapper Service.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected $streamWrapperService;

  /**
   * A request stack object.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * List of safe-list patterns.
   *
   * @var string[]|null
   */
  protected $patterns;

  /**
   * Creates a new VendorStreamWrapperManager instance.
   *
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface $stream_wrapper_service
   *   The Stream Wrapper Service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   A request stack object.
   * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(StreamWrapperManagerInterface $stream_wrapper_service, RequestStack $request_stack, EventDispatcherInterface $event_dispatcher) {
    $this->streamWrapperService = $stream_wrapper_service;
    $this->requestStack = $request_stack;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function getSafeListRegexPatterns(): array {
    if ($this->patterns === NULL) {
      // Collect the safe-list patterns by dispatching the appropriate event.
      $this->eventDispatcher->dispatch(
        new VendorStreamWrapperCollectSafeListRegexPatternsEvent($this),
        VendorStreamWrapperEvents::COLLECT_SAFE_LIST_REGEX_PATTERNS
      );
    }

    return $this->patterns;
  }

  /**
   * {@inheritdoc}
   */
  public function addSafeListRegexPatterns(array $patterns): void {
    $this->patterns = array_merge($this->patterns ?? [], $patterns);
  }

  /**
   * {@inheritdoc}
   */
  public function isSafeListed(string $file_path): bool {
    foreach ($this->getSafeListRegexPatterns() as $pattern) {
      // In case a valid pattern matches, indicate that the particular file is
      // allowed to be accessed.
      if (preg_match($pattern, $file_path)) {
        return TRUE;
      }
    }

    // No valid pattern matches the provided file path.
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function creatUrlFromUri(string $uri, bool $include_base_url = TRUE): ?string {
    @trigger_error(__METHOD__ . '() is deprecated in vendor_stream_wrapper:2.0.5 and is removed from vendor_stream_wrapper:3.0.0. Use createUrlFromUri() instead. See https://www.drupal.org/project/vendor_stream_wrapper/issues/3452824', E_USER_DEPRECATED);
    return $this->createUrlFromUri($uri, $include_base_url);
  }

  /**
   * {@inheritdoc}
   */
  public function createUrlFromUri(string $uri, bool $include_base_url = TRUE): ?string {
    if (strpos($uri, 'vendor://') === 0) {
      if ($wrapper = $this->streamWrapperService->getViaUri($uri)) {
        $url = $wrapper->getExternalUrl();
        if ($include_base_url) {
          return $url;
        }
        $base_url = $this->requestStack->getCurrentRequest()->getBaseUrl();
        return substr($url, strlen($base_url !== '/' ? $base_url : ''));
      }
    }
    else {
      return $uri;
    }

    return NULL;
  }

}
