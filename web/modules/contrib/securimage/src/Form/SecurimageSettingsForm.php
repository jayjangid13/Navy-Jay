<?php

/**
 * @file
 * Contains \Drupal\securimage\Form\SecurimageSettingsForm.
 */

namespace Drupal\securimage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\securimage\SecurimageMiddleware;

class SecurimageSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'securimage_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('securimage.settings');
    $old = SecurimageMiddleware::getSetting();

    foreach ($form_state->getValues() as $variable => $value) {
      if (isset($old[$variable])) {
        $config->set($variable, $value);
      }
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['securimage.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $vars = SecurimageMiddleware::getSetting();

    $form['securimage_example'] = [
      '#type' => 'fieldset',
      '#title' => t('Example'),
      '#description' => t('CAPTCHA example, generated with the current settings.'),
    ];
    $form['securimage_example']['image'] = [
      '#type' => 'captcha',
      '#captcha_type' => 'securimage/Securimage',
      '#captcha_admin_mode' => TRUE,
    ];

    // General code settings.
    $form['code'] = [
      '#type' => 'fieldset',
      '#title' => t('Code'),
    ];
    $form['code']['captcha_type'] = [
      '#type' => 'select',
      '#title' => t('Type'),
      '#options' => [
        \Securimage::SI_CAPTCHA_STRING => t('Random Characters'),
        \Securimage::SI_CAPTCHA_WORDS => t('Random Words'),
        \Securimage::SI_CAPTCHA_MATHEMATIC => t('Math Question'),
      ],
      '#default_value' => $vars['captcha_type'],
    ];
    $form['code']['string'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          '#edit-captcha-type' => [
            'value' => \Securimage::SI_CAPTCHA_STRING
            ]
          ]
        ],
    ];
    $form['code']['string']['charset'] = [
      '#type' => 'textfield',
      '#title' => t('Characters to use in the code'),
      '#default_value' => $vars['charset'],
      '#required' => TRUE,
      '#description' => t('WARNING: Do not add characters to this list without adding a sound file for each character. See <a href=":url">:url</a> for more info.', [
        ':url' => Url::fromUri('https://www.phpcaptcha.org/documentation/creating-audio-files/')->toString()
        ]),
    ];
    $form['code']['string']['code_length'] = [
      '#type' => 'select',
      '#title' => t('Code length'),
      '#options' => [
        2 => 2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        10,
      ],
      '#default_value' => $vars['code_length'],
      '#description' => t('Consider making the image wider as you increase the code length.'),
    ];
    $form['code']['string']['case_sensitive'] = [
      '#type' => 'checkbox',
      '#title' => t('Case-sensitive'),
      '#default_value' => $vars['case_sensitive'],
      '#description' => t('Require the user to enter the text case-sensitively. This option is not recommended for most cases, since users will fail the test more often. The audio option also does not support case-sensitive output, so not all users will be able to enter the text.'),
    ];
    $form['code']['words'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          '#edit-captcha-type' => [
            'value' => \Securimage::SI_CAPTCHA_WORDS
            ]
          ]
        ],
    ];
    $form['code']['words']['wordlist_file'] = [
      '#type' => 'textfield',
      '#title' => t('Word list file'),
      '#default_value' => $vars['wordlist_file'],
      '#description' => t('The full system path of the word list file, containing one word per line. Leave blank to use the default file that comes with Securimage.'),
    ];
    $form['code']['expiry_time'] = [
      '#type' => 'textfield',
      '#title' => t('Expiration time'),
      '#default_value' => $vars['expiry_time'],
      '#description' => t('The number of seconds, after which the code will become invalid. Enter 0 to disable automatic expiration.'),
      '#maxlength' => 8,
      '#size' => 8,
    ];
    $form['code']['_module'] = ['#tree' => TRUE];
    $form['code']['_module']['textfield_prompt'] = [
      '#type' => 'textfield',
      '#title' => t('Challenge description'),
      '#description' => t('Description of the CAPTCHA.'),
      '#default_value' => $vars['_module']['textfield_prompt'],
    ];

    // Image settings.
    $form['image'] = [
      '#type' => 'fieldset',
      '#title' => t('Image'),
      '#description' => t('Configuration of the background, text colors and file format of the image CAPTCHA.'),
    ];
    $form['image']['image_width'] = [
      '#type' => 'textfield',
      '#title' => t('Image width'),
      '#description' => t('The width, in pixels, of the generated image. A suggested value is 200.'),
      '#default_value' => $vars['image_width'],
      '#maxlength' => 4,
      '#size' => 4,
    ];
    $form['image']['image_height'] = [
      '#type' => 'textfield',
      '#title' => t('Image height'),
      '#description' => t('The height, in pixels, of the generated image. A suggested value is 80.'),
      '#default_value' => $vars['image_height'],
      '#maxlength' => 4,
      '#size' => 4,
    ];
    $form['image']['image_type'] = [
      '#type' => 'select',
      '#title' => t('File format'),
      '#description' => t('Select the file format for the image. JPEG usually results in smaller files.'),
      '#default_value' => $vars['image_type'],
      '#options' => [
        \Securimage::SI_IMAGE_JPEG => t('JPEG'),
        \Securimage::SI_IMAGE_PNG => t('PNG'),
        \Securimage::SI_IMAGE_GIF => t('GIF'),
      ],
    ];
    $form['image']['image_bg_color'] = [
      '#type' => 'textfield',
      '#title' => t('Background color'),
      '#description' => t('Enter the hexadecimal code for the background color (e.g. #FFF or #FFCE90).'),
      '#default_value' => $vars['image_bg_color'],
      '#maxlength' => 7,
      '#size' => 8,
    ];
    $form['image']['background_directory'] = [
      '#type' => 'textfield',
      '#title' => t('Background image directory'),
      '#default_value' => $vars['background_directory'],
      '#description' => t('The full system path to scan for background images. If set, a random background will be chosen from this folder.'),
    ];
    $form['image']['text_color'] = [
      '#type' => 'textfield',
      '#title' => t('Text color'),
      '#description' => t('Enter the hexadecimal code for the text color (e.g. #000 or #004283).'),
      '#default_value' => $vars['text_color'],
      '#maxlength' => 7,
      '#size' => 8,
    ];
    $form['image']['text_transparency_percentage'] = [
      '#type' => 'select',
      '#title' => t('Text transparency'),
      '#options' => [
        0 => t('solid'),
        10 => '10%',
        20 => '20%',
        30 => '30%',
        40 => '40%',
        50 => '50%',
        60 => '60%',
        70 => '70%',
        80 => '80%',
        90 => '90%',
      ],
      '#default_value' => $vars['text_transparency_percentage'],
      '#description' => t('Amount of transparency for the text.'),
    ];
    $form['image']['ttf_file'] = [
      '#type' => 'textfield',
      '#title' => t('Font TTF file'),
      '#default_value' => $vars['ttf_file'],
      '#description' => t('The full system path of the TTF font file. Leave blank to use the default AHGBold.ttf that comes with Securimage.'),
    ];

    // Distortion and noise settings.
    $form['distortion_and_noise'] = [
      '#type' => 'fieldset',
      '#title' => t('Distortion and noise'),
      '#description' => t('With these settings you can control the degree of obfuscation by distortion and added noise. It is generally not a good idea to combine high levels of both distortion and noise.'),
    ];
    $form['distortion_and_noise']['perturbation'] = [
      '#type' => 'select',
      '#title' => t('Distortion level'),
      '#options' => [
        '0.0' => t('@level - no distortion', ['@level' => '0']),
        '0.1' => t('@level - low', ['@level' => '1']),
        '0.2' => '2',
        '0.3' => '3',
        '0.4' => '4',
        '0.5' => t('@level - medium', ['@level' => '5']),
        '0.6' => '6',
        '0.7' => '7',
        '0.8' => '8',
        '0.9' => '9',
        '1.0' => t('@level - high', ['@level' => '10']),
      ],
      '#default_value' => intval(floor($vars['perturbation'] * 10.0)) / 10.0,
      '#description' => t('The degree of wave distortion in the image.'),
    ];
    $form['distortion_and_noise']['num_lines'] = [
      '#type' => 'select',
      '#title' => t('Number of lines'),
      '#options' => range(0, 10),
      '#default_value' => $vars['num_lines'],
      '#description' => t('The number of random lines to draw on top of the text.'),
    ];
    $form['distortion_and_noise']['line_color'] = [
      '#type' => 'textfield',
      '#title' => t('Line color'),
      '#description' => t('Enter the hexadecimal code for the line color (e.g. #777 or #707070).'),
      '#default_value' => $vars['line_color'],
      '#maxlength' => 7,
      '#size' => 8,
    ];
    $form['distortion_and_noise']['noise_level'] = [
      '#type' => 'select',
      '#title' => t('Noise level'),
      '#options' => [
        0 => t('@level - no noise', ['@level' => '0']),
        1 => t('@level - low', ['@level' => '1']),
        2 => '2',
        3 => '3',
        4 => '4',
        5 => t('@level - medium', ['@level' => '5']),
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => t('@level - high', ['@level' => '10']),
      ],
      '#default_value' => $vars['noise_level'],
      '#description' => t('The amount of random dots to draw on top of the text.'),
    ];
    $form['distortion_and_noise']['noise_color'] = [
      '#type' => 'textfield',
      '#title' => t('Noise (dot) color'),
      '#description' => t('Enter the hexadecimal code for the noise color (e.g. #777 or #707070).'),
      '#default_value' => $vars['noise_color'],
      '#maxlength' => 7,
      '#size' => 8,
    ];

    // Audio settings.
    $form['audio'] = [
      '#type' => 'fieldset',
      '#title' => t('Audio'),
    ];
    $form['audio']['_module'] = ['#tree' => TRUE];
    $form['audio']['_module']['use_audio'] = [
      '#type' => 'checkbox',
      '#title' => t('Use audio'),
      '#default_value' => $vars['_module']['use_audio'],
      '#description' => t('Add a button so that the challenge text can be spoken audibly using a Flash-based player. This allows visually impaired users to respond.'),
    ];
    $form['audio']['settings'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          '#edit-module-use-audio' => [
            'checked' => TRUE
            ]
          ]
        ],
    ];
    $form['audio']['settings']['audio_path'] = [
      '#type' => 'textfield',
      '#title' => t('Audio file path'),
      '#default_value' => $vars['audio_path'],
      '#description' => t('The full system path of the individual audio files for each text character. Leave blank to use the default files that come with Securimage. See <a href=":url">:url</a> for more info.', [
        ':url' => Url::fromUri('http://www.phpcaptcha.org/documentation/creating-audio-files/')->toString()
        ]),
    ];
    $form['audio']['settings']['audio_use_noise'] = [
      '#type' => 'checkbox',
      '#title' => t('Add background noise'),
      '#default_value' => $vars['audio_use_noise'],
      '#description' => t('Include random background noise in the audio, to make it more secure.'),
    ];
    $form['audio']['settings']['audio_noise'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          '#edit-audio-use-noise' => [
            'checked' => TRUE
            ]
          ]
        ],
    ];
    $form['audio']['settings']['audio_noise']['audio_noise_path'] = [
      '#type' => 'textfield',
      '#title' => t('Background noise path'),
      '#default_value' => $vars['audio_noise_path'],
      '#description' => t('The full system path of the directory containing audio files that will be selected randomly and mixed with the CAPTCHA audio.'),
    ];
    $form['audio']['settings']['degrade_audio'] = [
      '#type' => 'checkbox',
      '#title' => t('Add random noise'),
      '#default_value' => $vars['degrade_audio'],
      '#description' => t('Add random static, which may make the audio more secure.'),
    ];
    $form['audio']['gap'] = [
      '#type' => 'fieldset',
      '#title' => t('Pause between characters'),
      '#description' => t('A pause of random duration between each spoken character can help make the audio more secure.'),
      '#states' => [
        'visible' => [
          '#edit-module-use-audio' => [
            'checked' => TRUE
            ]
          ]
        ],
    ];
    $gaps = [
      0 => t('none'),
      100 => t('.1 sec'),
      250 => t('.25 sec'),
      500 => t('.5 sec'),
      750 => t('.75 sec'),
      1000 => t('1 sec'),
      1250 => t('1.25 sec'),
      1500 => t('1.5 sec'),
      1750 => t('1.75 sec'),
      2000 => t('2 sec'),
      2250 => t('2.25 sec'),
      2500 => t('2.5 sec'),
      2750 => t('2.75 sec'),
      3000 => t('3 sec'),
      3250 => t('3.25 sec'),
      3500 => t('3.5 sec'),
      3750 => t('3.75 sec'),
      4000 => t('4 sec'),
      4250 => t('4.25 sec'),
      4500 => t('4.5 sec'),
      4750 => t('4.75 sec'),
      5000 => t('5 sec'),
    ];
    $form['audio']['gap']['audio_gap_min'] = [
      '#type' => 'select',
      '#title' => t('Minimum gap'),
      '#options' => $gaps,
      '#default_value' => $vars['audio_gap_min'],
      '#description' => t('The lowest gap value.'),
    ];
    $form['audio']['gap']['audio_gap_max'] = [
      '#type' => 'select',
      '#title' => t('Maximum gap'),
      '#options' => $gaps,
      '#default_value' => $vars['audio_gap_max'],
      '#description' => t('The highest gap value.'),
    ];
    $form['audio']['lame_binary_path'] = [
      '#type' => 'textfield',
      '#title' => t('Path to the LAME encoder binary'),
      '#default_value' => $vars['lame_binary_path'],
      '#description' => t('The full system path of the LAME program, which is used to convert audio data into MP3 format. Without this, the WAV format is used, which is less compatible across browsers.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue(['_module', 'use_audio']) && $form_state->getValue([
      'lame_binary_path'
      ]) && !is_executable($form_state->getValue(['lame_binary_path']))) {
      $form_state->setError($form['audio']['lame_binary_path'], t('Either the LAME encoder binary path is not correct, it is not an executable program, or access is denied'));
    }
  }

}
