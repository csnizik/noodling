<?php

namespace Drupal\csv_import\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\asset\Entity\Asset;

/**
 * Producer form.
 */
class WorkbookDateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    
    $form['#attached']['library'][] = 'csv_import/workbook_date_form';

    $quarter_options = [
      'Jan 1 - March 31', 
      'April 1 - June 30',
      'July 1 - September 30',
      'October 1 - December 31',
    ];

    $year_options = [
      '2022', 
      '2023',
    ];

    $form['Choose Month of Reporting'] = [
      '#type' => 'select',
      '#title' =>'Choose Month of Reporting',
      '#required' => TRUE,
      '#options' => $quarter_options,
    ];

    $form['Choose Year of Reporting'] = [
      '#type' => 'select',
      '#title' =>'Choose Year of Reporting',
      '#required' => TRUE,
      '#options' => $year_options,
    ];

    $form['File Upload'] = [
      '#type' => 'file',
      '#title' =>'Add a File',
      '#description' =>'choose a file to upload',
      '#required' => TRUE,
      
    ];

    // $workbook_default_value = $is_edit ? $producer->get('project')->target_id : NULL;
    // $form['field_producer_project'] = [
    //   '#type' => 'textfield',
    //   '#title' =>'Workbook',
    //   '#required' => TRUE,
    //   '#default_value' => 'Asfsbdf',
    // ];

    
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
        '#submit' => ['::deleteProducer'],
      ];
    }

    return $form;


  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'workbook_date_form';
  }

  /**
   * {@inheritdoc}
   */
  
  public function SubmitForm(array &$form, FormStateInterface $form_state) {
    return 'workbook_date_form';
  }

}
