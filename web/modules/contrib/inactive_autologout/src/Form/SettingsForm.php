<?php

namespace Drupal\inactive_autologout\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Inactive logout settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\user\Entity\Role
   */
  protected $userRoleStorage;

  /**
   * Constructs a SettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
    $this->userRoleStorage = $this->entityTypeManager->getStorage('user_role');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'inactive_autologout.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inactive_autologout_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('inactive_autologout.settings');

    $form['autologout'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t("Auto Logout"),
    ];

    $form['autologout']['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#default_value' => ($config->get('enable') !== NULL) ? $config->get('enable') : FALSE,
      '#description' => $this->t('Enable autologout.'),
    ];

    $defaults = 120;
    if (!empty($config->get('timeout'))) {
      $defaults = $config->get('timeout');
    }

    $form['autologout']['timeout'] = [
      '#type' => 'number',
      '#title' => $this->t('Default Timeout value in seconds'),
      '#description' => $this->t('The length of inactivity time, in seconds, before automated log out. Must be 120 seconds or greater. Will not be used if role based timeout is enabled.'),
      '#default_value' => $defaults,
      '#required' => TRUE,
    ];

    $form['autologout']['roles'] = [
      '#type' => 'details',
      '#title' => $this->t('Roles'),
      '#open' => TRUE,
    ];

    $form['autologout']['roles']['role_based_timeout'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Role based timeout'),
      '#default_value' => ($config->get('enable') !== NULL) ? $config->get('role_based_timeout') : FALSE,
      '#description' => $this->t('To enable role based timeout, check this box.'),
    ];

    $roles = $this->getAvailableRoles();
    foreach ($roles as $id => $value) {
      $form['autologout']['roles'][$id] = [
        '#type' => 'checkbox',
        '#title' => $value,
        '#default_value' => (!empty($config->get($id))) ? $config->get($id) : FALSE,
      ];
      $form['autologout']['roles'][$id . '_timeout'] = [
        '#type' => 'number',
        '#title' => $this->t('Timeout value in seconds for @value role', ['@value' => $value]),
        '#default_value' => $config->get($id . '_timeout'),
        '#description' => $this->t('The length of inactivity time, in seconds, before automated log out. Must be 120 seconds or greater. Will be used if role based timeout is enabled.'),
        '#states' => [
          'visible' => [
            ':input[name="' . $id . '"]' => ['checked' => TRUE],
          ],
          'required' => [
            ':input[name="role_based_timeout"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $timeout = $form_state->getValue('timeout');
    if ($timeout < 120) {
      $form_state->setErrorByName('timeout', $this->t('Must be 120 or greater.'));
    }
    $role_based = $form_state->getValue('role_based_timeout');
    if ($role_based) {
      foreach ($this->getAvailableRoles() as $id => $value) {
        $role_timeout = $form_state->getValue($id . '_timeout');
        $role_enable = $form_state->getValue($id);
        if ($role_enable && $role_timeout < 120) {
          $form_state->setErrorByName($key . '_timeout', $this->t('@value timeout must be 120 or greater.', ['@value' => $value]));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $roles = $this->getAvailableRoles();
    $inactive_logout_config = $this->config('inactive_autologout.settings')
      ->set('timeout', $form_state->getValue('timeout'))
      ->set('enable', $form_state->getValue('enable'))
      ->set('role_based_timeout', $form_state->getValue('role_based_timeout'));
    foreach ($roles as $id => $value) {
      $inactive_logout_config->set($id, $form_state->getValue($id));
      $inactive_logout_config->set($id . '_timeout', $form_state->getValue($id . '_timeout'));
    }
    $inactive_logout_config->save();

  }

  /**
   * Get the available roles list.
   */
  public function getAvailableRoles() {
    $roles = [];
    $available_roles = $this->userRoleStorage->loadMultiple();
    foreach ($available_roles as $role) {
      $roles[$role->id()] = $role->label();
    }
    unset($roles['anonymous']);
    unset($roles['authenticated']);
    return $roles;
  }

}
