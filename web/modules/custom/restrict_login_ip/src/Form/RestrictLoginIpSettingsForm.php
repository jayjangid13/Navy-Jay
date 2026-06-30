<?php

namespace Drupal\restrict_login_ip\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the settings form for the Restrict Login IP module.
 */
class RestrictLoginIpSettingsForm extends ConfigFormBase {

  const SETTINGS = 'restrict_login_ip.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'restrict_login_ip_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [self::SETTINGS];
  }

  /**
   * Builds the configuration form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::SETTINGS);

    $form['ip_ranges'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed IP ranges'),
      '#description' => $this->t('Enter the allowed IP addresses ot ranges (in CIDR format), separated by semicolons. Example: 192.168.1.0/24;192.168.10.128;10.0.0.0/8'),
      '#default_value' => $config->get('ip_ranges') ?: '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Handles form submission.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(self::SETTINGS)
      ->set('ip_ranges', $form_state->getValue('ip_ranges'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
