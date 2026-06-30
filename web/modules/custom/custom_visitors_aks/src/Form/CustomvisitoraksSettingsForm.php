<?php

namespace Drupal\custom_visitors_aks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure custom_visitors_aks settings for this site.
 */
class CustomvisitoraksSettingsForm extends ConfigFormBase {

  /** 
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'custom_visitor_aks.settings';

  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_visitor_aks_admin_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);
		$form['lg_start_count'] = [
		  '#type' => 'textfield',
		  '#title' => 'visitor start pointer',
		  '#default_value' => $config->get('lg'),
		  '#pattern' => '^[1-9][0-9]*$',
		];  
    /*$form['domain_2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('domain 2'),
      '#default_value' => $config->get('domain_2'),
    ];*/ 

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    //$this->config(static::SETTINGS)
      // Set the submitted configuration setting.

      $this->config(static::SETTINGS)->set('lg', $form_state->getValue('lg_start_count'))->save();

      // You can set multiple configurations at once by making
      // multiple calls to set().
      //->set('domain_2', $form_state->getValue('domain_2'))
     

    parent::submitForm($form, $form_state);
  }

}