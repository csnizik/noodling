<?php

namespace Drupal\cig_pods\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing;
use Drupal\asset\Entity\AssetInterface;
use Drupal\asset\Entity\Asset;
use Drupal\Core\Url;

class ConfirmDeleteModalForm extends FormBase {

  public function getFormId() {
    return 'confirm_delete_modal_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['markup'] = [
      '#type' => 'markup',
      '#markup' => $this->t('<br><div >This item will be permanently deleted. You cannot undo this action.</div>'),

    ];
    
    $form['actions']['no'] = [
      '#type' => 'button',
      '#value' => $this->t('No, go back'),
      '#limit_validation_errors' => [],
      '#attributes' => ['id' => ['edit-cancel'], 'class'=> ['button popup-close-button']],
    ];

    $form['actions']['confirmDeletePopup'] = [
      '#type' => 'submit',
      '#value' => $this->t('Yes, delete'),
      '#attributes' => ['id' => ['edit-delete'], 'class'=> ['button dc-popup']],
    ];

    return $form;
  }



  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}