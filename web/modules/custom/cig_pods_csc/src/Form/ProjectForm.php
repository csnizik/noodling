<?php

namespace Drupal\cig_pods_csc\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\asset\Entity\Asset;
use \Drupal\user\Entity\User;

/**
 * Project form.
 */
class ProjectForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    
    $project = $asset;
    $is_edit = $project <> NULL;

    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('project_id', $project->id());
    }
    else {
      $form_state->set('operation', 'create');
    }

    // Attach proper CSS to form.
    $form['#attached']['library'][] = 'cig_pods_csc/grantee_project_setup_form';
    $form['#attached']['library'][] = 'cig_pods_csc/css_form';
    $form['#attached']['library'][] = 'core/drupal.form';


    $form['form_title'] = [
      '#markup' => '<h1>Project</h1>',
    ];



    $project_default_name = $is_edit ? $project->get('csc_project_id_field')->value : '';

    $form['csc_project_id'] = [
      '#type' => 'textfield',
      '#title' =>'Project ID',
      '#required' => TRUE,
      '#default_value' =>$project_default_name,
    ];

    $org_name_default = $is_edit ? $project->get('csc_project_grantee_org')->value : '';
    
    $form['csc_project_org_name'] = [
      '#type' => 'textfield',
      '#title' =>'Organization Name',
      '#required' => TRUE,
      '#default_value' => $org_name_default,
    ];

    $csc_project_grantee_cont_name_default = $is_edit ? $project->get('csc_project_grantee_cont_name')->value : '';
    
    $form['csc_project_grantee_cont_name'] = [
      '#type' => 'textfield',
      '#title' =>'Grantee Primary Point of Contact',
      '#required' => TRUE,
      '#default_value' => $csc_project_grantee_cont_name_default,
    ];

    $csc_project_grantee_cont_email_default = $is_edit ? $project->get('csc_project_grantee_cont_email')->value : '';
    
    $form['csc_project_grantee_cont_email'] = [
      '#type' => 'textfield',
      '#title' =>'Grantee Primary Point of Contact Email',
      '#required' => TRUE,
      '#default_value' => $csc_project_grantee_cont_email_default,
    ];

    $csc_project_comet_version_default = $is_edit ? $project->get('csc_project_comet_version')->value : '';
    
    $form['csc_project_comet_version'] = [
      '#type' => 'textfield',
      '#title' =>'Organization Name',
      '#required' => TRUE,
      '#default_value' => $csc_project_comet_version_default,
    ];




    $fav_default = NULL;

    if ($is_edit){

      $aid = $project->id();

      $user = User::load(\Drupal::currentUser()->id());

      $field_value = $user->get('award_favorites');

      $existing_ids = array_column($field_value->getValue(), 'target_id');

      if (in_array($aid, $existing_ids)) {

        $fav_default = 1;

      }else{
        $fav_default = 0;
      }

    }
    $form['favorite_radios'] = [
      '#type' => 'checkbox',
      '#required' => FALSE,
      '#title' => $this->t('Mark as favorite'),
      '#default_value' => $fav_default,
    ];
    
    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#limit_validation_errors' => '',
      '#submit' => ['::dashboardRedirect'],

    ];

    if ($is_edit) {
      $form['actions']['delete'] = [
        '#type' => 'submit',
        '#value' => $this->t('Delete'),
        '#submit' => ['::deleteProject'],
      ];
    }

    return $form;


  }
  

  /**
   * Redirect to the PODS dashboard.
   */
  public function dashboardRedirect(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('<front>');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'project_create_form';
  }

  /**
   * Delete project.
   */
  public function deleteProject(array &$form, FormStateInterface $form_state) {
    $project_id = $form_state->get('project_id');
    $project = \Drupal::entityTypeManager()->getStorage('asset')->load($project_id);

    try {
      $project->delete();
      $form_state->setRedirect('cig_pods.dashboard');
    }
    catch (\Exception $e) {
      $this
        ->messenger()
        ->addError($e->getMessage());
    }

  }


  /**
   * {@inheritdoc}
   */
  
  public function SubmitForm(array &$form, FormStateInterface $form_state) {

    
    $is_create = $form_state->get('operation') === 'create';
   
    if ($is_create) {
    $project_submission = [];
    $project_submission['name'] = $form_state->getValue('csc_project_id');
    $project_submission['csc_project_id_field'] = $form_state->getValue('csc_project_id');
    $project_submission['csc_project_grantee_org'] = $form_state->getValue('csc_project_org_name');

    $project_submission['csc_project_grantee_cont_name'] = $form_state->getValue('csc_project_grantee_cont_name');
    $project_submission['csc_project_grantee_cont_email'] = $form_state->getValue('csc_project_grantee_cont_email');
    $project_submission['csc_project_comet_version'] = $form_state->getValue('csc_project_comet_version');

    $project_submission['type'] = 'csc_project';

    $project = Asset::create($project_submission);
    $project->save();


    $aid = $project->id();

    $fav = $form['favorite_radios']['#value'];



    if($fav){


      $user = User::load(\Drupal::currentUser()->id());

      $user->award_favorites[] = ['target_id' => $aid];

      $user->save();

    }


    $form_state->setRedirect('<front>');

  }
  else {

    $project_id = $form_state->get('project_id');
    $project = \Drupal::entityTypeManager()->getStorage('asset')->load($project_id);

    $project_name = $form_state->getValue('csc_project_id');
    $csc_project_id = $form_state->getValue('csc_project_id');
    $csc_org_name = $form_state->getValue('csc_project_org_name');
    
    $csc_cont_name = $form_state->getValue('csc_project_grantee_cont_name');
    $csc_cont_email = $form_state->getValue('csc_project_grantee_cont_email');
    $csc_comet_version = $form_state->getValue('csc_project_comet_version');


    $project->set('name', $project_name);
    $project->set('csc_project_id_field', $csc_project_id);
    $project->set('csc_project_grantee_org', $csc_org_name);
    $project->set('csc_project_grantee_cont_name', $csc_cont_name);
    $project->set('csc_project_grantee_cont_email', $csc_cont_email);
    $project->set('csc_project_comet_version', $csc_comet_version);
    $project->save();


    $aid = $project->id();

    $fav = $form['favorite_radios']['#value'];

    $user = User::load(\Drupal::currentUser()->id());

    if($fav){

      $existing_ids = array_column($user->award_favorites->getValue(), 'target_id');

      if (!in_array($aid, $existing_ids)) {
        
        $user->award_favorites[] = ['target_id' => $aid];

        $user->save();

      }

    }else{

      $field_value = $user->get('award_favorites');
      
      $existing_ids = array_column($field_value->getValue(), 'target_id');

      $remove_id = array_search($aid, $existing_ids);


      if (in_array($aid, $existing_ids)) {

        $field_value->removeItem($remove_id);

        $user->save();          

      }

    }


    $form_state->setRedirect('<front>');


  }

  }

}
