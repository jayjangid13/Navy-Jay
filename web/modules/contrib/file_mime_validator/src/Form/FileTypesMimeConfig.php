<?php

namespace Drupal\file_mime_validator\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Render\Element;

/**
 * Class of File Types Mime Configuration.
 */
class FileTypesMimeConfig extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'file_types_mime_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['file_mime_validator.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('file_mime_validator.settings');

    $form['file_mime_validator_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Text'),
      '#rows' => 5,
      '#cols' => 10,
      '#required' => TRUE,
      '#default_value' => $config->get('file_mime_validator_text'),
      '#description' => $this->t('Enter the relative mime types for Text. For Example: text/plain,text/vnd.in3d.3dml,text/x-c++src "," separated with no spaces.'),
    ];
    $form['file_mime_validator_image'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Image'),
      '#rows' => 5,
      '#cols' => 10,
      '#required' => TRUE,
      '#default_value' => $config->get('file_mime_validator_image'),
      '#description' => $this->t('Enter the relative mime types for Image. For Example: "image/cgm,image/cdr,image/x-cmx,image/x-cdr" must be "," separated with no spaces.'),
    ];
    $form['file_mime_validator_compression'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Compression'),
      '#rows' => 5,
      '#cols' => 10,
      '#required' => TRUE,
      '#default_value' => $config->get('file_mime_validator_compression'),
      '#description' => $this->t('Enter the relative mime types for Compression. For Example: "application/zip,application/x-rar,application/x-zip,application/vnd.rar" must be "," separated with no spaces.'),
    ];
    $form['file_mime_validator_audio'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Audio'),
      '#rows' => 5,
      '#cols' => 10,
      '#required' => TRUE,
      '#default_value' => $config->get('file_mime_validator_audio'),
      '#description' => $this->t('Enter the relative mime types for Audio. For Example: "audio/3gpp,audio/3gpp2,audio/vnd.audible,audio/x-rn-3gpp-amr" must be "," separated with no spaces.'),
    ];
    $form['file_mime_validator_video'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Video'),
      '#rows' => 5,
      '#cols' => 10,
      '#required' => TRUE,
      '#default_value' => $config->get('file_mime_validator_video'),
      '#description' => $this->t('Enter the relative mime types for Video. For For Example: "video/3gp,video/3gpp,video/mp2t,video/3gpp2" must be "," separated with no spaces.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('file_mime_validator.settings');

    foreach (Element::children($form) as $variable) {
      $config->set($variable, $form_state->getValue($form[$variable]['#parents']));
    }
    $config->save();

    drupal_flush_all_caches();
  }

}
