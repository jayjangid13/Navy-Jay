<?php

namespace Drupal\restrict_login_ip\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Modifies existing routes to add custom access requirements.
 */
class RestrictLoginIpRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Alter the user login route to add our custom access check and disable caching.
    if ($route = $collection->get('user.login')) {
      $route->setRequirement('_restrict_login_ip_access', 'TRUE');
      $route->setOption('no_cache', 'TRUE');
    }
  }

}
