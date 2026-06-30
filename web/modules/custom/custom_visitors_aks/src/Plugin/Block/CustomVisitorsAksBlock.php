<?php

namespace Drupal\custom_visitors_aks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Visitors' block.
 *
 * @Block(
 *   id = "custom_visitors_aks_block",
 *   admin_label = @Translation("Visitors Aks"),
 *   category = @Translation("Visitors Aks")
 * )
 */
class CustomVisitorsAksBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
	$config = \Drupal::config('custom_visitor_aks.settings');
  $query = \Drupal::database()->select('count_visits', 'c');
  $query->fields('c', ['record_count']);
  $count = $query->countQuery()->execute()->fetchField();
  $extra_val = $config->get('lg') ? $config->get('lg') : 0;
    return [
      'custom_visitors_aks_info' => [
        '#theme' => 'custom_visitors_aks_info',
        '#items' => $count+$extra_val,
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }
	/**
	 * {@inheritdoc}
	 */
    public function getCacheMaxAge() {
        return 0;
    }
  /**
   * Display total visitors count to visitors block.
   */
  protected function showTotalVisitors() {
    if ($this->config->get('show_total_visitors')) {
      $query = \Drupal::database()->select('custom_visitors_aks');
      $query->addExpression('COUNT(*)');

      $count = $query->execute()->fetchField() +
        $this->config->get('start_count_total_visitors');

      $this->items[] = $this->t('Total Visitors: %visitors', ['%visitors' => $count]);
    }
  }

  /**
   * Display unique visitors count to visitors block.
   */
  protected function showUniqueVisitors() {
    if ($this->config->get('show_unique_visitor')) {
      $query = \Drupal::database()->select('custom_visitors_aks');
      $query->addExpression('COUNT(DISTINCT visitors_ip)');

      $unique_visitors = $query->execute()->fetchField();

      $this->items[] = $this->t('Unique Visitors: %unique_visitors', ['%unique_visitors' => $unique_visitors]);
    }
  }

  /**
   * Display registered users count to visitors block.
   */
  protected function showRegisteredUsersCount() {
    if ($this->config->get('show_registered_users_count')) {
      $query = \Drupal::database()->select('users');
      $query->addExpression('COUNT(*)');
      $query->condition('uid', '0', '>');

      $registered_users_count = $query->execute()->fetchField();

      $this->items[] = $this->t('Registered Users: %registered_users_count', ['%registered_users_count' => $registered_users_count]);
    }
  }

  /**
   * Display last registered user to visitors block.
   */
  protected function showLastRegisteredUser() {
    if ($this->config->get('show_last_registered_user')) {
      $last_user_uid = \Drupal::database()->select('users', 'u')
        ->fields('u', ['uid'])
        ->orderBy('uid', 'DESC')
        ->range(0, 1)
        ->execute()
        ->fetchField();

      $user = \Drupal::entityTypeManager()->getStorage('user')->load($last_user_uid);
      $username = [
        '#theme' => 'username',
        '#account' => $user,
      ];

      $this->items[] = $this->t('Last Registered User: @last_user',
        ['@last_user' => \Drupal::service('renderer')->render($username)]);
    }
  }

  /**
   * Display published nodes count to visitors block.
   */
  protected function showPublishedNodes() {
    if ($this->config->get('show_published_nodes')) {
      $query = \Drupal::database()->select('node', 'n');
      $query->innerJoin('node_field_data', 'nfd', 'n.nid = nfd.nid');
      $query->addExpression('COUNT(*)');
      $query->condition('nfd.status', '1', '=');

      $nodes = $query->execute()->fetchField();

      $this->items[] = $this->t('Published Nodes: %nodes', ['%nodes' => $nodes]);
    }
  }

  /**
   * Display user ip to visitors block.
   */
  protected function showUserIp() {
    if ($this->config->get('show_user_ip')) {
      $this->items[] = $this->t('Your IP: %user_ip', ['%user_ip' => \Drupal::request()->getClientIp()]);
    }
  }

  /**
   * Display the start date statistics to visitors block.
   */
  protected function showSinceDate() {
    if ($this->config->get('show_since_date')) {
      $query = \Drupal::database()->select('custom_visitors_aks');
      $query->addExpression('MIN(visitors_date_time)');

      $since_date = $query->execute()->fetchField();

      $this->items[] = $this->t('Since: %since_date', [
        '%since_date' => \Drupal::service('date.formatter')->format($since_date, 'short'),
      ]);
    }
  }

}
