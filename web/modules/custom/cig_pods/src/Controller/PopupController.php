<?php

namespace Drupal\cig_pods\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\asset\Entity\Asset;

class PopupController extends ControllerBase {

  public function openConfirmModal() {
    $response = new AjaxResponse();
    $modal_form = $this->formBuilder()->getForm('Drupal\cig_pods\Form\ConfirmModalForm');
    $options = [
      'width' => '50%',
      'classes' => 'confirm-modal-popup',
      'dialogClass' => 'confirm-dialog-popup',
    ];
    $response->addCommand(new OpenModalDialogCommand('Are you sure you want to cancel?', $modal_form, $options));
    return $response;
  }
  
    public function openDeleteConfirmModal($asset_id) {

    $asset_type = \Drupal::entityTypeManager()->getStorage('asset')->load($asset_id)->type->entity->label();

    $response = new AjaxResponse();
    
    $modal_form = $this->formBuilder()->getForm('Drupal\cig_pods\Form\ConfirmDeleteModalForm');
    $options = [
      'width' => '50%',
      'classes' => 'confirm-modal-popup',
      'dialogClass' => 'confirm-dialog-popup',
    ];
    $response->addCommand(new OpenModalDialogCommand('Are you sure you want to delete '.$asset_type.'?', $modal_form, $options));
    return $response;
  }

}