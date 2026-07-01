<?php

namespace Drupal\security_headers\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscribes to the Kernel Response event to add security headers.
 */
class SecurityHeadersSubscriber implements EventSubscriberInterface {

  /**
   * Sanitizes reflected query parameters before Drupal builds page links.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The event to process.
   */
  public function onRequest(RequestEvent $event) {
    if (!$event->isMainRequest()) {
      return;
    }

    $request = $event->getRequest();
    $query = $request->query->all();
    $path = $request->getPathInfo();
    $target_path = $this->sanitizeSearchPath($path);
    $changed = FALSE;

    if (isset($query['keys']) && is_scalar($query['keys'])) {
      $safe_keys = $this->sanitizeSearchKeys((string) $query['keys']);
      if ($safe_keys === '') {
        unset($query['keys']);
      }
      else {
        $query['keys'] = $safe_keys;
      }
      $changed = $safe_keys !== (string) $request->query->get('keys');
    }

    if (isset($query['language']) && !in_array($query['language'], ['en', 'hi'], TRUE)) {
      unset($query['language']);
      $changed = TRUE;
    }

    if ($target_path !== $path) {
      $changed = TRUE;
    }

    if ($changed) {
      $target = $target_path;
      if ($query) {
        $target .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
      }
      $event->setResponse(new RedirectResponse($target, 302));
    }
  }

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

    $response->headers->set('Content-Security-Policy', implode('; ', [
      "default-src 'self'",
      "base-uri 'self'",
      "object-src 'none'",
      "script-src 'self'",
      "script-src-attr 'none'",
      "style-src 'self'",
      "img-src 'self' data:",
      "font-src 'self' data:",
      "connect-src 'self'",
      "frame-ancestors 'self'",
      "form-action 'self'",
      'upgrade-insecure-requests',
    ]));


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
      KernelEvents::REQUEST => ['onRequest', 100],
      KernelEvents::RESPONSE => ['onRespond', -1000],
    ];
  }

  /**
   * Removes characters that are not useful for search and risky in hrefs.
   *
   * @param string $keys
   *   User-entered search text.
   *
   * @return string
   *   Sanitized search text.
   */
  private function sanitizeSearchKeys(string $keys): string {
    $keys = html_entity_decode($keys, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $keys = strip_tags($keys);
    $keys = preg_replace('/[^\p{L}\p{N}\s._@#&:\/-]+/u', ' ', $keys);
    $keys = preg_replace('/\s+/u', ' ', $keys);

    return trim(mb_substr($keys, 0, 128));
  }

  /**
   * Sanitizes the search path argument used by /search/node/{keys}.
   *
   * @param string $path
   *   Request path.
   *
   * @return string
   *   Safe request path.
   */
  private function sanitizeSearchPath(string $path): string {
    if (!preg_match('#^(/hi)?/search/node/(.+)$#', $path, $matches)) {
      return $path;
    }

    $prefix = $matches[1] ?? '';
    $raw_keys = rawurldecode($matches[2]);
    $safe_keys = $this->sanitizeSearchKeys($raw_keys);

    if ($safe_keys === '' || $safe_keys === $raw_keys) {
      return $path;
    }

    return $prefix . '/search/node/' . rawurlencode($safe_keys);
  }

}
