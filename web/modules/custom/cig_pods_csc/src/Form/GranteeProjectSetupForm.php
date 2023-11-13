<?php

namespace Drupal\cig_pods_csc\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\asset\Entity\Asset;

/**
 * Producer form.
 */
class GranteeProjectSetupForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    
    // Attach proper CSS to form.
    $form['#attached']['library'][] = 'cig_pods_csc/grantee_project_setup_form';
    $form['#attached']['library'][] = 'cig_pods_csc/css_form';
    $form['#attached']['library'][] = 'core/drupal.form';


    $form['form_title'] = [
      '#markup' => '<h1>Grantee Project Setup</h1>',
    ];

    $form['subform_1'] = [
      '#markup' => '<div class="subform-title-container subform-title-container-top"><h2>Project Info</h2><h4>2 Field | Section 1 of 3</h4></div>',
    ];
    $form['static']['project_info_description'] = [
      '#markup' => '<div class="grantee-project-descriotion">By entering the Project ID, you will initlate the project in our system. When grantees
      successfully import their workbooks, the Coversheet data will be attached to this
      Project ID.</div>',
    ];


    $form['csc_project_id'] = [
      '#type' => 'textfield',
      '#title' =>'Project ID',
      '#required' => TRUE,
      '#default_value' =>'',
    ];

    $form['csc_project_org_name'] = [
      '#type' => 'textfield',
      '#title' =>'Organization Name',
      '#required' => TRUE,
      '#default_value' => '',
    ];


    $form['subform_2'] = [
      '#markup' => '<div class="subform-title-container section3"><h2>Grantee POC</h2><h4>3 Fields | Section 2 of 3</h4> </div>',
    ];

    $form['static_1']['grante_poc_description'] = [
      '#markup' => '<div class="grantee-project-descriotion" >By adding the grantee contact, you are giving this user access to submit their
      workbooks. Only 1 grantee POC can be added to each project.</div>',
    ];


    $form['csc_grantee_poc_name'] = [
      '#type' => 'textfield',
      '#title' =>'Grantee Name',
      '#required' => TRUE,
      '#default_value' => '',
    ];

    $form['csc_grantee_poc_eauth'] = [
      '#type' => 'textfield',
      '#title' =>'Grantee eAuth ID',
      '#required' => TRUE,
      '#default_value' => '',
    ];


    $form['csc_grantee_poc_email'] = [
      '#type' => 'textfield',
      '#title' =>'Grantee Email',
      '#required' => TRUE,
      '#default_value' => '',
    ];


    $form['subform_3'] = [
      '#markup' => '<div class="subform-title-container section3"><h2>Project contact - NPO</h2><h4>2 Fields | Section 3 of 3</h4> </div>',
    ];


    $form['static_2']['project_contact_description'] = [
      '#markup' => "<div class='grantee-project-descriotion' >It's optional to add NPO to project. Lead NPO and Backup NPO are designated here
      only for ease of management.</div>",
    ];


    $form['csc_lead_npo'] = [
      '#type' => 'textfield',
      '#title' =>'Lead NPO',
      '#required' => FALSE,
      '#default_value' => '',
    ];


    $form['csc_backup_npo'] = [
      '#type' => 'textfield',
      '#title' =>'Backup NPO',
      '#required' => FALSE,
      '#default_value' => '',
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
    return 'grantee_project_create_form';
  }

  /**
   * {@inheritdoc}
   */
  
  public function SubmitForm(array &$form, FormStateInterface $form_state) {

    

   

    $project_submission = [];
    $project_submission['name'] = $form_state->getValue('csc_project_id');
    $project_submission['csc_project_id_field'] = $form_state->getValue('csc_project_id');
    $project_submission['csc_project_grantee_org'] = $form_state->getValue('csc_project_org_name');
    $project_submission['csc_project_grantee_cont_name'] = $form_state->getValue('csc_grantee_poc_name');
    $project_submission['csc_project_grantee_cont_email'] = $form_state->getValue('csc_grantee_poc_email');
    $project_submission['type'] = 'csc_project';

    $project = Asset::create($project_submission);
    $project->save();

    $form_state->setRedirect('<front>');

  }

}
