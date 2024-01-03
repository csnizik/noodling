<?php

namespace Drupal\cig_pods_csc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\asset\Entity\Asset;
use Drupal\user\Entity\User;

class FavoritesController extends ControllerBase {

  public function getfavorites() {
    $user = User::load(\Drupal::currentUser()->id());
    $field_value = $user->get('award_favorites')->getValue();

if(!$user->get('award_favorites')->isEmpty()){

    $favs = '';
    foreach($field_value as $key=> $value){

      $asset = Asset::load($value['target_id']);
      
      $favs .= '<tr><td>'.$value['target_id'].'</td><td><a href="/edit/project/'.$value['target_id'].'">'.$asset->label().'</a></td></tr>';

    }

    $output = '<table><tr><th>ID</th><th>Project name</th></tr>'.$favs.'</table>';

  }else{

    $output = "No Projects are marked as favorite.";
    // $favs = "";

  }
    return [
      '#children' => '<div style="width: 600px;" class="view-content gin-layer-wrapper">'.$output.'</div>',
    ];
    
  }


}