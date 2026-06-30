<?php

/**
 * @file
 * Contains Drupal\push_notifications\PushNotificationsTokenQuery.
 */

namespace Drupal\push_notifications;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PushNotificationsTokenQuery {

  /**
   * An instance of the entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * PushNotificationsTokenQuery constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_query
   */
  public function __construct(EntityTypeManagerInterface $entity_query) {
    $this->entityTypeManager = $entity_query;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Get the push notification tokens by user ID.
   *
   * @param array $uids
   *   User IDs.
   * @return array|null
   */
  public function getTokensByUid($uids) {
    if (!is_array($uids)) {
      return NULL;
    }

    $push_notifications_token_storage = $this->entityTypeManager->getStorage('push_notifications_token');
    $push_notifications_token = $push_notifications_token_storage->loadByProperties(array('uid' => $uids));
    $tokens = array();

    foreach ($push_notifications_token as $pid => $push_notification_token) {
      array_push($tokens, $push_notification_token->getToken());
    }

    return $tokens;
  }

  /**
   * Get the push notification tokens by network.
   *
   * @param array $networks
   *   Push Networks.
   * @return array|null
   */
  public function getTokensByNetwork($networks) {
    if (!is_array($networks)) {
      return NULL;
    }

    $push_notifications_token_storage = $this->entityTypeManager->getStorage('push_notifications_token');
    $push_notifications_token = $push_notifications_token_storage->loadByProperties(array('network' => $networks));

    // Retrieve all tokens into array.
    $tokens = array();
    foreach ($push_notifications_token as $pid => $push_notification_token) {
      array_push($tokens, $push_notification_token->getToken());
    }

    return $tokens;
  }

  /**
   * Get all the push notification tokens.
   *
   * @return array
   */
  public function getAllTokens() {
    $push_notifications_token_storage = $this->entityTypeManager->getStorage('push_notifications_token');
    $push_notifications_token = $push_notifications_token_storage->loadMultiple();

    // Retrieve all tokens into array.
    $tokens = array();
    foreach ($push_notifications_token as $pid => $push_notification_token) {
      array_push($tokens, $push_notification_token->getToken());
    }

    return $tokens;
  }

}
