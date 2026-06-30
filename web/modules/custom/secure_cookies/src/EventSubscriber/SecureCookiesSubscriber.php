<?php

namespace Drupal\secure_cookies\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\Cookie;

class SecureCookiesSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      KernelEvents::RESPONSE => 'onResponse',
    ];
  }

  public function onResponse(ResponseEvent $event) {
    $request = $event->getRequest();
    $path = $request->getPathInfo();

    // Decide cookie path dynamically
    $cookiePath = '/';
    if (str_starts_with($path, '/admin')) {
      $cookiePath = '/admin';
    }

    $response = $event->getResponse();
    $cookies = $response->headers->getCookies();

    foreach ($cookies as $cookie) {
      $name = $cookie->getName();

      if (
        $name === 'localtimestamp' ||
        $name === 'servertimestamp' ||
        str_starts_with($name, 'twk_uuid_')
      ) {

        $newCookie = Cookie::create(
          $name,
          $cookie->getValue(),
          $cookie->getExpiresTime(),
          $cookiePath,                 // <-- path now dynamic
          $cookie->getDomain(),
          true,
          false,
          false,
          'Strict'
        );

        $response->headers->setCookie($newCookie);
      }
    }
  }
}

// namespace Drupal\secure_cookies\EventSubscriber;

// use Symfony\Component\HttpKernel\KernelEvents;
// use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// use Symfony\Component\HttpKernel\Event\ResponseEvent;
// use Symfony\Component\HttpFoundation\Cookie;

// class SecureCookiesSubscriber implements EventSubscriberInterface {

//   public static function getSubscribedEvents() {
//     return [
//       KernelEvents::RESPONSE => 'onResponse',
//     ];
//   }

//   public function onResponse(ResponseEvent $event) {
//     $response = $event->getResponse();
//     $cookies = $response->headers->getCookies();

//     foreach ($cookies as $cookie) {
//       $name = $cookie->getName();

//       if (
//         $name === 'localtimestamp' ||
//         $name === 'servertimestamp' ||
//         str_starts_with($name, 'twk_uuid_')
//       ) {
//         $newCookie = Cookie::create(
//           $name,
//           $cookie->getValue(),
//           $cookie->getExpiresTime(),
//           '/',
//           $cookie->getDomain(),
//           true,
//           false,
//           false,
//           'Strict'
//         );

//         $response->headers->setCookie($newCookie);
//       }
//     }
//   }
// }
