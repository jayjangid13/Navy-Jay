<?php
 
namespace Drupal\indian_navy_api\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;
 
class PressReleaseController extends ControllerBase {
 
  public function getPressReleases(Request $request) {
    // Get POST data
    $name 			= $request->get('name', 'test');
    $email 			= $request->get('email', 'test@gmail.com');
    $password 		= $request->get('password', '*****');
    $deviceType 	= $request->get('device_type', '');
    $deviceToken 	= $request->get('device_token', '');
    $fcmToken 		= $request->get('fcm_token', '');
    $type 			= $request->get('type', '');
 
    // Query to get 'school_slider' content type nodes
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'press_release')
      ->condition('status', 1)
      ->accessCheck(TRUE);
    $nids = $query->execute();
    $nodes = Node::loadMultiple($nids);
 
    $data = [];
    foreach ($nodes as $node) {
      $data[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        // 'body' => $node->get('body')->value,
        // Add other fields as necessary
      ];
    }
 
    // Formulate the response
    $response = [
      'success' => 1,
      'message' => 'Data List',
      'results' => [
        [
          'name' 			=> $name,
          'email' 			=> $email,
          'password' 		=> $password,
          'device_type' 	=> $deviceType,
          'device_token' 	=> $deviceToken,
          'fcm_token' 		=> $fcmToken,
          'type' 			=> $type,
          'content' 		=> $data,
        ]
      ]
    ];
 
    return new JsonResponse($response);
  }
  
  public function getLatestUpdate(Request $request) {
    // Get POST data
    $name 			= $request->get('name', 'test');
    $email 			= $request->get('email', 'test@gmail.com');
    $password 		= $request->get('password', '*****');
    $deviceType 	= $request->get('device_type', '');
    $deviceToken 	= $request->get('device_token', '');
    $fcmToken 		= $request->get('fcm_token', '');
    $type 			= $request->get('type', '');
 
    // Query to get 'school_slider' content type nodes
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'latest_update')
      ->condition('status', 1)
      ->accessCheck(TRUE);
    $nids = $query->execute();
    $nodes = Node::loadMultiple($nids);
 
    $data = [];
    foreach ($nodes as $node) {
      $data[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        // 'body' => $node->get('body')->value,
        // Add other fields as necessary
      ];
    }
 
    // Formulate the response
    $response = [
      'success' => 1,
      'message' => 'Data List',
      'results' => [
        [
          'name' 			=> $name,
          'email' 			=> $email,
          'password' 		=> $password,
          'device_type' 	=> $deviceType,
          'device_token' 	=> $deviceToken,
          'fcm_token' 		=> $fcmToken,
          'type' 			=> $type,
          'content' 		=> $data,
        ]
      ]
    ];
 
    return new JsonResponse($response);
  }
  
}
  