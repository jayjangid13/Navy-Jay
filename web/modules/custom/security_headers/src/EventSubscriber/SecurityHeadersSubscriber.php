<?php

namespace Drupal\security_headers\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscribes to the Kernel Response event to add security headers.
 */
class SecurityHeadersSubscriber implements EventSubscriberInterface {

  /**
   * Adds security headers to the response.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The event to process.
   */
  public function onRespond(ResponseEvent $event) {
    $response = $event->getResponse();
    
    $response->headers->remove('Server');
    
    // X-XSS-Protection: Enable XSS filtering.
    $response->headers->set('X-XSS-Protection', '1; mode=block');

    // Content-Security-Policy: Define the content security policy.
    // $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self';");

    $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;");

    // $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");


    // Referrer-Policy: Control referrer information sent with requests.
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

    // X-Frame-Options: Prevent framing of the site to mitigate clickjacking.
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

    // X-Content-Type-Options: Prevent MIME-type sniffing.
    $response->headers->set('X-Content-Type-Options', 'nosniff');

    // Permissions-Policy: Limit browser features (previously called Feature-Policy).
    $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=()');

    // Strict-Transport-Security (HSTS): Enforce HTTPS.
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
    
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
