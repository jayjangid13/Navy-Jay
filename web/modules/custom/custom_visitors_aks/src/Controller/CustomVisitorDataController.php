<?php

namespace Drupal\custom_visitors_aks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\MenuLinkTree;
use Drupal\Core\Menu\DefaultMenuLinkTreeManipulators;

/**
* Provides route responses for the Sitemap page module.
*/

class CustomVisitorDataController extends ControllerBase {
 /**
  * Returns a sitemap page.
  *
  * @return array
  *   A simple renderable array.
  */
 public function getData() {
    $database = \Drupal::database();
	$query = $database->select('custom_visitors_aks', 'cv');
	$query->fields('cv', ['visitors_ip']);
	$result = $query->execute();
	$f_result = array();
	foreach($result as $vip){
		$f_result[] = $vip->visitors_ip;
	}
    return [
        '#theme' => 'custom_visitordata',
        '#v_data' => $result
    ];
 }
 /**
   * Returns a page title.
   */
  public function getTitle() {
    // Get current language code
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    //$title = t('Sitemap');
    switch($language) {
      case 'en':
        $title = 'Sitemap';
        break;
      case 'hi':
        $title = 'साइट मैप';
        break;
    }
    return  $title;
  }
}