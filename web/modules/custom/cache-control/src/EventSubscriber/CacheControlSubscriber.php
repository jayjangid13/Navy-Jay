<?php

namespace Drupal\cache_control\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscribes to the Kernel Response event to add cache control headers.
 */
class CacheControlSubscriber implements EventSubscriberInterface {

  /**
   * Adds Cache-Control and Pragma headers to the response.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The event to process.
   */
  public function onRespond(ResponseEvent $event) {
    $response = $event->getResponse();

    // Set the Cache-Control header.
    $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
    // Set the Pragma header.
    $response->headers->set('Pragma', 'no-cache');
    // Optional: Set the Expires header to a past date.
    $response->headers->set('Expires', '0');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Listen to the kernel.response event.
    return [
      KernelEvents::RESPONSE => 'onRespond',
    ];
  }

}
