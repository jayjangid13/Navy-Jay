<?php

namespace Drupal\all_in_one_accessibility\Form;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\InfoParser;
use Drupal\Core\Url;

/**
 * Provide settings page for adding CSS/JS before the end of body tag.
 */
class UseridForm extends ConfigFormBase {

  /**
   * Implements FormBuilder::getFormId.
   */
  public function getFormId() {
    return 'all_in_one_accessibility';
  }

  /**
   * Implements ConfigFormBase::getEditableConfigNames.
   */
  protected function getEditableConfigNames() {
    return ['all_in_one_accessibility.userid.settings'];
  }

  /**
   * Implements FormBuilder::buildForm.
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {

    $aioa_host_info =  \Drupal::request()->getHost();

    $allinone_userid = $this->config('all_in_one_accessibility.userid.settings')->get();

    if(!isset($allinone_userid['userid']) || empty(trim($allinone_userid['userid']))){

      $url = "https://www.skynettechnologies.com/add-ons/discount_offer.php?platform=drupal";
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      $resp = curl_exec($curl);
      curl_close($curl);


     $form['allinone']['notes'] = [
        '#type' => 'processed_text',
        '#text' => $resp . "<style>.ada-banner-section{padding-left: 15px; padding-right: 15px; margin-bottom: 20px; max-width:1300px;} .inner-wrapper .intro .text .title{line-height: 1.5;} .inner-wrapper .intro .text ul li{ line-height: 1.7; }</style>",
        '#format' => 'full_html',
     ];
    }

    $form['allinone']['userid'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('License key required for full version'),
      '#default_value' => isset($allinone_userid['userid']) ? $allinone_userid['userid'] : '',
      '#description'   => empty($allinone_userid['userid']) ? $this->t('Please <a href="https://www.skynettechnologies.com/add-ons/cart/?add-to-cart=116&variation_id=117&quantity=1&utm_source='. $aioa_host_info . '&utm_medium=drupal-module&utm_campaign=trial-subscription" target="_blank">Upgrade</a> to full version of All in One Accessibility Pro.') : '',
      '#rows'          => 10,
    ];
    $form['allinone']['nofreeversion'] = [
      '#type'          => 'hidden',
      '#default_value' => 1
    ];

    $form['allinone']['colorcode'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Hex color code'),
      '#default_value' => isset($allinone_userid['colorcode']) ? $allinone_userid['colorcode'] : '',
      '#description'   => $this->t('<p>You can cutomize the ADA Widget color. For example: #FF5733'),
      '#rows'          => 10,
    ];


    $options = array(
      'bottom_right' => $this->t('Bottom Right'),
      'bottom_left' => $this->t('Bottom left'),
      'bottom_center' => $this->t('Bottom Center'),
      'middel_left' => $this->t('Middle left'),
      'middel_right' => $this->t('Middle Right'),
      'top_left' => $this->t('Top left'),
      'top_center' => $this->t('Top Center'),
      'top_right' => $this->t('Top Right')
    );

    $form['allinone']['position'] = [
      '#type' => 'radios',
      '#title' => $this->t('Where would you like to place the accessibility icon on your site?'),
      '#options' => $options,
      '#default_value' => isset($allinone_userid['position']) ? $allinone_userid['position'] : 'bottom_right',
    ];

    if(!empty($allinone_userid['userid'])){

      $options1 = array(
        'aioa-icon-type-1' => $this->t('<img class="aioaicontype" src="https://www.skynettechnologies.com/sites/default/files/python/aioa-icon-type-1.svg" width="65" height="65" />'),
        'aioa-icon-type-2' => $this->t('<img class="aioaicontype" src="https://www.skynettechnologies.com/sites/default/files/python/aioa-icon-type-2.svg" width="65" height="65" />'),
        'aioa-icon-type-3' => $this->t('<img class="aioaicontype" src="https://www.skynettechnologies.com/sites/default/files/python/aioa-icon-type-3.svg" width="65" height="65" />'),
       );

      if(!isset($allinone_userid['aioa_icon_type']))
      {
        $allinone_userid['aioa_icon_type'] = "aioa-icon-type-1";
      }
      $form['allinone']['aioa_icon_type'] = [
        '#type' => 'radios',
        '#title' => $this->t('Select Icon Type:'),
        '#options' => $options1,
        '#default_value' => isset($allinone_userid['aioa_icon_type']) ? $allinone_userid['aioa_icon_type'] : 'aioa-icon-type-1',
      ];

      if (!isset($allinone_userid['aioa_icon_size'])) {
        $allinone_userid['aioa_icon_size'] = "aioa-default-icon";
      }
      $options2 = array(
        'aioa-big-icon' => $this->t('<img class="aioaiconsize" src="https://www.skynettechnologies.com/sites/default/files/python/'.$allinone_userid['aioa_icon_type'].'.svg" width="75" height="75" />'),
        'aioa-medium-icon' => $this->t('<img class="aioaiconsize" src="https://www.skynettechnologies.com/sites/default/files/python/' . $allinone_userid['aioa_icon_type'] . '.svg" width="65" height="65" />'),
        'aioa-default-icon' => $this->t('<img class="aioaiconsize" src="https://www.skynettechnologies.com/sites/default/files/python/' . $allinone_userid['aioa_icon_type'] . '.svg" width="55" height="55" />'),
        'aioa-small-icon' => $this->t('<img class="aioaiconsize" src="https://www.skynettechnologies.com/sites/default/files/python/' . $allinone_userid['aioa_icon_type'] . '.svg" width="45" height="45" />'),
        'aioa-extra-small-icon' => $this->t('<img class="aioaiconsize" src="https://www.skynettechnologies.com/sites/default/files/python/' . $allinone_userid['aioa_icon_type'] . '.svg" width="35" height="35" />'),
      );

      $form['allinone']['aioa_icon_size'] = [
        '#type' => 'radios',
        '#title' => $this->t('Select Icon Size:'),
        '#options' => $options2,
        '#default_value' => isset($allinone_userid['aioa_icon_size']) ? $allinone_userid['aioa_icon_size'] : 'aioa-icon-type-1',
        '#description'   => $this->t('<script>
            const sizeOptionsImg = document.querySelectorAll(".aioaiconsize");
            const typeOptions = document.querySelectorAll("input[name=\'aioa_icon_type\']");
                    typeOptions.forEach(option => {
                        option.addEventListener("change", (event) => {
                            sizeOptionsImg.forEach(option2 => {
                                var ico_type = document.querySelector("input[name=\'aioa_icon_type\']:checked").value;
                                option2.setAttribute("src", "https://www.skynettechnologies.com/sites/default/files/python/" + ico_type + ".svg");
                            });
                        });
                    });
        </script>
        <style>
        /* Radio Button Css */
          #edit-aioa-icon-type input,
          #edit-aioa-icon-size input {
            position: absolute;
            /*left: -9999px;*/
			opacity: 0;
          }
          #edit-aioa-icon-type .form-item,
          #edit-aioa-icon-size .form-item {
            margin-left: 0;
			position:relative;
          }
          #edit-aioa-icon-type input[type=radio]+label,
          #edit-aioa-icon-size input[type=radio]+label {
            width: 130px;
            height: 130px;
            padding: 10px !important;
            text-align: center;
            background-color: #f7f9ff;
            outline: 4px solid #f7f9ff;
            outline-offset: -4px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 12px;
            margin-right: 12px;
          }
          #edit-aioa-icon-type input[type=radio]:checked+label,
          #edit-aioa-icon-size input[type=radio]:checked+label {
            outline-color: #80c944;
            position: relative;
          }
          #edit-aioa-icon-type input[type=radio]:checked+label::before,
          #edit-aioa-icon-size input[type=radio]:checked+label::before {
            content: "";
            width: 20px;
            height: 20px;
            position: absolute;
            left: auto;
            right: -4px;
            top: -4px;
            background: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 25 25\' class=\'aioa-feature-on\'%3E%3Cg%3E%3Ccircle fill=\'%2343A047\' cx=\'12.5\' cy=\'12.5\' r=\'12\'%3E%3C/circle%3E%3Cpath fill=\'%23FFFFFF\' d=\'M12.5,1C18.9,1,24,6.1,24,12.5S18.9,24,12.5,24S1,18.9,1,12.5S6.1,1,12.5,1 M12.5,0C5.6,0,0,5.6,0,12.5S5.6,25,12.5,25S25,19.4,25,12.5S19.4,0,12.5,0L12.5,0z\'%3E%3C/path%3E%3C/g%3E%3Cpolygon fill=\'%23FFFFFF\' points=\'9.8,19.4 9.8,19.4 9.8,19.4 4.4,13.9 7.1,11.1 9.8,13.9 17.9,5.6 20.5,8.4 \'%3E%3C/polygon%3E%3C/svg%3E") no-repeat center center/contain !important;
            border: none;
          }
          /* IMAGE STYLES */
          #edit-aioa-icon-type label>img,
          #edit-aioa-icon-size label>img {
            cursor: pointer;
          }
          #edit-aioa-icon-type label,
          #edit-aioa-icon-size label {
            display: flex;
            justify-content: center;
            height: 90px;
            width: 90px;
            border: 2px solid gray;
            border-radius: 3px;
          }

          #edit-aioa-icon-type,
          #edit-aioa-icon-size {
            display: flex;
          }
          #edit-position {
            max-width: 520px;
            display: flex;
            flex-wrap: wrap;
            }
            #edit-position .form-item{
            width: 33.33333%;
            }
        </style> ')
      ];
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * Implements FormBuilder::submitForm().
   *
   * Serialize the user's settings and save it to the Drupal's config Table.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $url = "https://www.skynettechnologies.com/add-ons/license-api.php?";
    $postdata['token'] = $values['userid'];
    $postdata['SERVER_NAME'] = parse_url(Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(), PHP_URL_HOST);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($curl);
    $resp = json_decode($resp);


    if (empty($resp->accessibilityloader) && !empty(trim($values['userid']))) {
      $values['userid'] = "";
      $values['aioa_icon_type'] = (!empty($values['aioa_icon_type']) ? $values['aioa_icon_type'] : "aioa-icon-type-1");
      $values['aioa_icon_size'] =  (!empty($values['aioa_icon_size']) ? $values['aioa_icon_size'] : "aioa-default-icon");
      $this->messenger()->addStatus($this->t('Invalid License Key.'));
    } else {
      $this->messenger()->addStatus($this->t('Your Settings have been saved.'));
    }

    if(!isset($values['aioa_icon_type'])){
      $values['aioa_icon_type'] =  "aioa-icon-type-1";
    }
    if(!isset($values['aioa_icon_size'])){
      $values['aioa_icon_size'] =  "aioa-default-icon";
    }  

    $this->configFactory()
      ->getEditable('all_in_one_accessibility.userid.settings')
      ->set('userid', $values['userid'])
      ->save();

    $this->configFactory()
      ->getEditable('all_in_one_accessibility.userid.settings')
      ->set('colorcode', $values['colorcode'])
      ->save();

    $this->configFactory()
      ->getEditable('all_in_one_accessibility.userid.settings')
      ->set('position', $values['position'])
      ->save();

    $this->configFactory()
      ->getEditable('all_in_one_accessibility.userid.settings')
      ->set('aioa_icon_type', $values['aioa_icon_type'])
      ->save();

    $this->configFactory()
      ->getEditable('all_in_one_accessibility.userid.settings')
      ->set('aioa_icon_size', $values['aioa_icon_size'])
      ->save();

    $this->configFactory()
    ->getEditable('all_in_one_accessibility.userid.settings')
    ->set('nofreeversion', $values['nofreeversion'])
    ->save();

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://ada.skynettechnologies.us/api/widget-setting-update-platform',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'u' => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
        'widget_position' => $values['position'],
        'widget_color_code' => $values['colorcode'],
        'widget_icon_type' => (!empty($values['aioa_icon_type']) ? $values['aioa_icon_type'] : "aioa-icon-type-1"),
        'widget_icon_size' => (!empty($values['aioa_icon_size']) ? $values['aioa_icon_size'] : "aioa-medium-icon")
      ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    drupal_flush_all_caches();
  }
}
