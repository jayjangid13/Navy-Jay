<?php
 
namespace Drupal\redirct_url\EventSubscriber;
 
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
 
class LanguageRedirectSubscriber implements EventSubscriberInterface {
 
  /**
   * Handle redirections based on the `?language` query parameter.
   */
  public function onRequest(RequestEvent $event) {
    $request = $event->getRequest();
 
    // Check if `?language` parameter is present.
    $language = $request->query->get('language');
 
    // Get the current path.
    $current_path = $request->getPathInfo();
 
    // Check if the language is 'en'.
    if ($language === 'en') {
      // Redirect to the base path if the current path contains '/hi'.
      if (strpos($current_path, '/hi') === 0) {
        $base_path = '/'; // Adjust this base path as needed.
        $response = new \Symfony\Component\HttpFoundation\RedirectResponse($base_path, 301);
        $event->setResponse($response);
      }
      return;
    }
 
    // If the language is 'hi', perform the redirect to '/hi'.
    if ($language === 'hi') {
      // Check if the requested path is an asset (JS, CSS, or other static files).
      if (preg_match('/\.(js|css|jpg|jpeg|png|gif|ico|svg)$/', $current_path)) {
        // Do not redirect for asset requests (JS, CSS, etc.).
        return;
      }
 
      // Build the new URL with '/hi' as a subdirectory.
      $new_path = '/hi' . $current_path;
 
      // Perform the redirect.
      $response = new \Symfony\Component\HttpFoundation\RedirectResponse($new_path, 301);
      $event->setResponse($response);
    }
  }
 
  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['onRequest', 100],
    ];
  }
}

// namespace Drupal\redirct_url\EventSubscriber;
 
// use Symfony\Component\HttpKernel\KernelEvents;
// use Symfony\Component\HttpKernel\Event\RequestEvent;
// use Symfony\Component\EventDispatcher\EventSubscriberInterface;
 
// class LanguageRedirectSubscriber implements EventSubscriberInterface {
 
// /**
// * Redirect from `?language=hi` to `/hi` with the correct subdirectory, but
// * avoid redirecting for JS, CSS, or image requests.
// */
// public function onRequest(RequestEvent $event) {
// $request = $event->getRequest();
 
// // Check if `?language` parameter is present.
// $language = $request->query->get('language');
 
// // If the language is 'hi', perform the redirect
// if ($language === 'hi') {
// // Get the current path (after the `?language=hi` query string).
// $current_path = $request->getPathInfo();
 
// // Check if the requested path is an asset (JS, CSS, or other static files).
// if (preg_match('/\.(js|css|jpg|jpeg|png|gif|ico|svg)$/', $current_path)) {
// // Do not redirect for asset requests (JS, CSS, etc.)
// return;
// }
 
// // Build the new URL with '/hi' as a subdirectory.
// $new_path = '/hi' . $current_path;
 
// // Perform the redirect.
// $response = new \Symfony\Component\HttpFoundation\RedirectResponse($new_path, 301);
// $event->setResponse($response);
// }
// }
 
// /**
// * {@inheritdoc}
// */
// public static function getSubscribedEvents() {
// return [
// KernelEvents::REQUEST => ['onRequest', 100],
// ];
// }
// }

// namespace Drupal\redirct_url\EventSubscriber;

// use Symfony\Component\HttpKernel\KernelEvents;
// use Symfony\Component\HttpKernel\Event\RequestEvent;
// use Symfony\Component\EventDispatcher\EventSubscriberInterface;

// class LanguageRedirectSubscriber implements EventSubscriberInterface {

//   /**
//    * Redirect from `?language=hi` to `/hi` with the correct subdirectory.
//    */
//   public function onRequest(RequestEvent $event) {
//     $request = $event->getRequest();

//     // Check if `?language` parameter is present.
//     $language = $request->query->get('language');
//     if ($language === 'hi') {
//       // Build the new URL including the base path and preserving the existing path.
//       $current_path = $request->getPathInfo();  // Get the path after '?language=hi'
//       $new_path = '/hi' . $current_path;

//       // Perform the redirect.
//       $response = new \Symfony\Component\HttpFoundation\RedirectResponse($new_path, 301);
//       $event->setResponse($response);
//     }
//   }

//   /**
//    * {@inheritdoc}
//    */
//   public static function getSubscribedEvents() {
//     return [
//       KernelEvents::REQUEST => ['onRequest', 100],
//     ];
//   }
// }
