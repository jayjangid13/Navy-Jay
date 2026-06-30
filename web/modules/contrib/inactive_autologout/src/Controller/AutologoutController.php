<?php

namespace Drupal\inactive_autologout\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Controller routines for user routes.
 */
class AutologoutController extends ControllerBase {

  /**
   * The request stacks service.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs Autologout object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack service.
   */
  public function __construct(
    RequestStack $request_stack
  ) {
    $this->requestStack = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
    );
  }

  /**
   * Logs the current user out.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirection to home page.
   */
  public function logout() {
    user_logout();
    return $this->redirect('user.login');
  }

  /**
   * Logs the current user out.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirection to home page.
   */
  public function autologout() {
    user_logout();
    return $this->redirect('user.login', [
      'autologout' => 'true',
      'absolute' => TRUE,
    ]);
  }

  /**
   * Store the active session.
   */
  public function autologoutActive(Request $request) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $timestamp = $request->query->get('localtimestamp');
      $session = $this->requestStack->getSession();
      $session->set('timestamp', $timestamp);
      if (!empty($session->get('timestamp'))) {
        $response['timestamp'] = $timestamp;
      }
    }
    else {
      $response['timestamp'] = '';
    }
    return new JsonResponse($response);
  }

  /**
   * Store the active session.
   */
  public function autologoutGetTimestamp() {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $session = $this->requestStack->getSession();
      $timestamp = $session->get('timestamp');
      $response['timestamp'] = $timestamp;
    }
    else {
      $response['timestamp'] = '';
    }
    return new JsonResponse($response);
  }

}
