<?php

namespace Drupal\custom_visitors_aks\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Store visitors data when a request terminates.
 */
class CustomVisitorAksSubscriber implements EventSubscriberInterface {
  /**
   * The currently active request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Database Service Object.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The title resolver.
   *
   * @var \Drupal\Core\Controller\TitleResolverInterface
   */
  protected $titleResolver;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The admin context service.
   *
   * @var \Drupal\Core\Routing\AdminContext
   */
  protected $adminContext;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Database\Connection $database
   *   The database service object.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match object.
   * @param \Drupal\Core\Controller\TitleResolverInterface $title_resolver
   *   The title_resolver service object.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config_factory service object.
   * @param \Drupal\Core\Routing\AdminContext $admin_context
   *   The admin context object.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler object.
   */
  public function __construct(AccountInterface $current_user, Connection $database, RouteMatchInterface $route_match, TitleResolverInterface $title_resolver, ConfigFactoryInterface $config_factory, AdminContext $admin_context, ModuleHandlerInterface $module_handler) {
    $this->currentUser = $current_user;
    $this->database = $database;
    $this->routeMatch = $route_match;
    $this->titleResolver = $title_resolver;
    $this->configFactory = $config_factory;
    $this->adminContext = $admin_context;
    $this->moduleHandler = $module_handler;
  }

  /**
   * Store visitors data when a request terminates.
   *
   * @param \Symfony\Component\HttpKernel\Event\TerminateEvent $event
   *   The Event to process.
   */
  public function onTerminate(TerminateEvent $event) {
    $this->request = $event->getRequest();
    if (!is_null($this->currentUser)) {
      $not_admin = !in_array('administrator', $this->currentUser->getRoles());
    }
    else {
      $not_admin = TRUE;
    }
    if ($not_admin) {
      $ip_str = $this->getIpStr();
      $fields = [
        'visitors_uid'        => $this->currentUser->id(),
        'visitors_ip'         => $ip_str,
        'visitors_date_time'  => time(),
        'visitors_url'        => $this->getUrl(),
        'visitors_referer'    => $this->getReferer(),
        'visitors_path'       => Url::fromRoute('<current>')->toString(),
        'visitors_title'      => $this->getTitle(),
        'visitors_user_agent' => $this->getUserAgent(),
		    'ip_main' => $this->request->getClientIp(),
      ];
      try {

      // Upated Code
      if($this->getUserAgent() != 'Site24x7'){
        $date_now = date('y-m-d');
        $query = \Drupal::database()->select('custom_visitors_aks', 'c');
        $query->fields('c', ['visitors_date_time']);
        $query->condition('c.visitors_ip', $ip_str, '=');
        $query->orderBy('c.visitors_id', 'DESC');
        $query->range(0, 1);
        $vdata = $query->execute()->fetchField();
        $vdate = date('y-m-d', $vdata);
        if($date_now == $vdate){
          }
        else{
        $this->database->insert('custom_visitors_aks')
        ->fields($fields)
        ->execute();
      //   $query = \Drupal::database()->select('custom_visitors_aks', 'c');
      //   $query->fields('c', array('visitors_id'));
      //   $num_rows = $query->countQuery()->execute()->fetchField();
      //   if($num_rows > 0){
      //   $this->database->update('count_visits')
      //   ->fields(['record_count' => $num_rows,])
      //   ->execute();
      // }
      // else{
      //   $this->database->insert('count_visits')
      //   ->fields(['record_count' => $num_rows,])
      //   ->execute();
      // }
        }
        }
      // # Updated Code
      
      }
      catch (\Exception $e) {

      }
    }
  }

  /**
   * Registers the methods in this class that should be listeners.
   *
   * @return array
   *   An array of event listener definitions.
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::TERMINATE][] = ['onTerminate'];
    return $events;
  }

  /**
   * Get the title of the current page.
   *
   * @return string
   *   Title of the current page.
   */
  protected function getTitle() {
    $title = '';
    $routeObject = $this->routeMatch->getRouteObject();
    if (!is_null($routeObject)) {
      $title = $this->titleResolver->getTitle($this->request, $routeObject);
    }

    if (is_array($title)) {
      return htmlspecialchars_decode($title['#markup'] ?? '', ENT_QUOTES);
    }

    return htmlspecialchars_decode($title ?? '', ENT_QUOTES);
  }

  /**
   * Get full path request uri.
   *
   * @return string
   *   Full path.
   */
  protected function getUrl() {
    $host = array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '';
    $uri = $this->request->getRequestUri();

    return urldecode(sprintf('http://%s%s', $host, $uri));
  }

  /**
   * Get the address of the page.
   *
   * If any, which referred the user agent to the current page.
   *
   * @return string
   *   Referer, or empty string if referer does not exist.
   */
  protected function getReferer() {
    return isset($_SERVER['HTTP_REFERER']) ? urldecode($_SERVER['HTTP_REFERER']) : '';
  }

  /**
   * Converts a string.
   *
   * Containing a visitors (IPv4) Internet Protocol dotted
   * address into a proper address.
   *
   * @return string
   *   IPv4) Internet Protocol dotted address.
   */
  protected function getIpStr() {
    return sprintf("%u", ip2long($this->request->getClientIp()));
  }

  /**
   * Get visitor user agent.
   *
   * @return string
   *   string user agent, or empty string if user agent does not exist
   */
  protected function getUserAgent() {
    return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
  }

  /**
   * Retrieve geoip data for ip.
   *
   * @param string $ip
   *   A string containing an ip address.
   *
   * @return array
   *   Geoip data array.
   */
  protected function getGeoipData($ip) {
    $result = [
      'continent_code' => '',
      'country_code'   => '',
      'country_code3'  => '',
      'country_name'   => '',
      'region'         => '',
      'city'           => '',
      'postal_code'    => '',
      'latitude'       => '0',
      'longitude'      => '0',
      'dma_code'       => '0',
      'area_code'      => '0',
    ];

    if (function_exists('geoip_record_by_name')) {
      $data = @geoip_record_by_name($ip);
      if ((!is_null($data)) && ($data !== FALSE)) {
        /* Transform city value from iso-8859-1 into the utf8. */
        $data['city'] = utf8_encode($data['city']);

        $result = $data;
      }
    }

    return $result;
  }

}
