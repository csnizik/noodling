<?php

namespace Drupal\cig_pods_csc\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\asset\Entity\Asset;
use \Drupal\user\Entity\User;
use Drupal\Core\Url;

/**
 * Awardee Landing Page.
 */
class AwardeeLandingPage extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    
    // Attach proper CSS to form.
    $form['#attached']['library'][] = 'cig_pods_csc/awardee_landing_page';


    $form['awardee_welcome'] = [
      '#markup' => '<div class="bottom-form"> 
      <h2 id="your_project">Your Project ID: Test Proj
        <div id="your_organization">Organization Name: Test Name
        </div>
      </h2>
      <div id="welcome_text">
      <p>You have been added as a grantee POC for your organization by a National Project Office (NPO).<br> 
      To submit reporting workbooks, you must be your organization\'s designated reporting point of contact.<br>
      If your project ID is incorrect, please email your NPO contact to resolve the issue.</p>
      </div>
      </div>',
    ];

    $form['interaction_section'] = [
      '#prefix' => '<div class="interactive-form">',
      '#markup' => '<div> 
      <h2 id="starting">Getting Started</h2>
      </div>',
    ];

    $form['actions']['submit_workbook'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Reporting Workbook(s)'),
      '#limit_validation_errors' => '',
      '#submit' => ['::importFormRedirect'],
    ];

    $form['actions']['view_import_history'] = [
      '#type' => 'submit',
      '#value' => $this->t('View Import History'),
      '#limit_validation_errors' => '',
      '#submit' => ['::importHistoryRedirect'],
      '#suffix' => '</div>',
    ];

    return $form;


  }
  

  /**
   * Redirect to the Import Form in the CSC CSV Import Module.
   * Note this method will break if the Import Module is not installed.
   */
  public function importFormRedirect(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('csc_csv_import.submit_workbooks');
  }

  /**
   * Redirect to the Import History View, displays the whole Import History on the site.
   */
  public function importHistoryRedirect(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirectUrl(Url::fromUri('internal:' . '/assets/csc_import_history'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'awardee_landing_page';
  }



  /**
   * {@inheritdoc}
   */
  
  public function SubmitForm(array &$form, FormStateInterface $form_state) {
  }

}
