<?php

namespace Drupal\restrict_login_ip\Access;

use Drupal\Core\Access\AccessCheckInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Access check for restricting login page access by IP.
 */
class RestrictLoginIpAccessCheck implements AccessCheckInterface {

  /**
   * The configuration factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The request stack service.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a RestrictLoginIpAccessCheck object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RequestStack $request_stack) {
    $this->configFactory = $config_factory;
    $this->requestStack = $request_stack;
  }

  /**
   * Determines if this access checker applies to the given route.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route to check.
   *
   * @return bool
   *   TRUE if the access checker applies to the route, FALSE otherwise.
   */
  public function applies(Route $route) {
    return $route->getRequirement('_restrict_login_ip_access') !== NULL;
  }

  /**
   * Access callback to restrict login page based on IP address.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The current route.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(Route $route, AccountInterface $account) {
    $config = $this->configFactory->get('restrict_login_ip.settings');
    $ip_ranges = $config->get('ip_ranges');

    // Allow access if no IP ranges are set.
    if (empty($ip_ranges)) {
      return AccessResult::allowed();
    }

    $ip_ranges_array = array_filter(array_map('trim', explode(';', $ip_ranges)));

    // Get the current request to obtain the client IP.
    $request = $this->requestStack->getCurrentRequest();
    if (!$request) {
      // If the request is not available, deny access as a precaution.
      return AccessResult::forbidden();
    }

    $client_ip = $request->getClientIp();

    if (IpUtils::checkIp($client_ip, $ip_ranges_array)) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

}
