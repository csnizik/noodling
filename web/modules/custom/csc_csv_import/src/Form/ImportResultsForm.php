<?php

namespace Drupal\csc_csv_import\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\Http\MultipartFormDataRequest;
use Symfony\Component\HttpFoundtion\Request;
use Symfony\Component\HttpFoundtion\Response;
use Drupal\views\Views;
use Drupal\Core\Url;

/**
 * Excel Import Results Form.
 */
class ImportResultsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $entity = \Drupal::entityTypeManager()->getStorage('asset')->load($id);
    $filename = $entity->csc_file_uploaded->value;
    $workbook_type = $entity->csc_workbook_type->value;
    $year = $entity->csc_year_of_reporting->getValue()[0]['numerator'];
    $quarter = $entity->csc_month_of_reporting->getValue()[0]['value'];

    $data = \Drupal::entityTypeManager()->getStorage('log')->loadByProperties(['type'=>'csc_import_details', 'csc_import_history_reference'=>$id]);

    foreach ($data as $d) {
      $details[] = [
        'sheetname' => $d->csc_import_sheetname->value,
        'record_cnt' => $d->csc_import_record_cnt->getValue()[0]['numerator'],
        'updated_cnt' => $d->csc_import_updated_cnt->getValue()[0]['numerator'],
        'type' => $d->csc_import_entity_type->value,
        'machine_name' => $d->csc_import_machine_name->value,
        'record_before' => $d->csc_import_records_before->getValue()[0]['numerator'],
      ];
    }

    $total_created = 0;
    $total_updated = 0;
    $total_before = 0;
    $total_after = 0;
    $total_deleted = 0;

    foreach ($details as $worksheet) {
      $total_before += $worksheet['record_before'];
      $total_created += $worksheet['record_cnt'] - $worksheet['updated_cnt'];
      $total_updated += $worksheet['updated_cnt'];
    }

    $total_after = $total_before + $total_created - $total_deleted;

    $form['#attached']['library'][] = 'csc_csv_import/import_results';
    $form['#attributes']['enctype'][] = 'multipart/form-data';


    # Three main sections of the form
    $form['success_banner_section'] = [
      '#type' => 'container',
      '#suffix' => '</div>',
    ];

    $form['more_workbook_section'] = [
      '#type' => 'container',
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#attributes' => ['class' => ['bottom-form']],
    ];

    $form['import_details_section'] = [
      '#type' => 'container',
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#attributes' => ['class' => ['bottom-form', 'section-contianer', 'row']],
    ];

    #-----------------------------------------------------------------------------

    $form['success_banner_section']['title'] = [
      '#prefix' => '<h5 id="banner_title">',
      '#suffix' => '</h5>',
      '#markup' => 'Import Completed',
    ];

    $form['success_banner_section']['message'] = [
      '#markup' => 'Your file "' . $filename . '" has been successfully imported.',
    ];

    #-----------------------------------------------------------------------------

    $form['more_workbook_section']['instructions'] = [
      '#type' => 'container',
      '#markup' => 'Please remember to upload both the <b>project reporting workbook</b> and <b>supplemental reporting workbook</b>.',
      '#attributes' => ['class' => ['more-workbook-instructions']],
    ];
    $form['more_workbook_section']['button'] = [
      '#type' => 'submit',
      '#value' => 'Import Another Workbook    >',
      '#submit' => ['::submitForm'],
      '#attributes' => ['id' => ['more-workbook-button']],
    ];

    #-----------------------------------------------------------------------------

    $form['import_details_section']['titleset'] = [
      '#type' => 'container',
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];

    $form['import_details_section']['infoset'] = [
      '#type' => 'container',
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#attributes' => ['class' => ['container']],
    ];

    $form['import_details_section']['summaryset'] = [
      '#type' => 'container',
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#attributes' => ['class' => ['container']],
    ];

    $form['import_details_section']['titleset']['title'] = [
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
      '#markup' => 'You have successfully imported the following records:',
    ];

    $form['import_details_section']['infoset']['type'] = [
      '#type' => 'container',
      '#prefix' => '<div class="row">',
      '#markup' => '<b>Workbook Type:</b> ' . $workbook_type,
      '#attributes' => ['class' => ['col', 'workbook-basic-info']],
    ];

    $form['import_details_section']['infoset']['year'] = [
      '#type' => 'container',
      '#markup' => '<b>Year of Reporting:</b> ' . $year,
      '#attributes' => ['class' => ['col', 'workbook-basic-info']],
    ];

    $form['import_details_section']['infoset']['months'] = [
      '#type' => 'container',
      '#suffix' => '</div>',
      '#markup' => '<b>Months of Reporting:</b> ' . $quarter,
      '#attributes' => ['class' => ['col', 'workbook-basic-info']],
    ];

    $form['import_details_section']['summaryset']['summary'] = [
      '#type' => 'container',
      '#prefix' => '<div class = "row"><div class = "summary-info">',
      '#suffix' => '</div',
      '#attributes' => ['class' => ['col', 'import-summary']],
    ];

    $form['import_details_section']['summaryset']['summary']['title'] = [
      '#type' => 'container',
      '#prefix' => '<h5>',
      '#suffix' => '</h5>',
      '#markup' => 'Summary',
      '#attributes' => ['class' => ['record_summary_title']],
    ];
    $form['import_details_section']['summaryset']['summary']['total_before'] = [
      '#type' => 'container',
      '#markup' => "Total number of rows before import: <div>" . $total_before . "</div>",
      '#attributes' => ['class' => ['record_summary']],
    ];
    $form['import_details_section']['summaryset']['summary']['total_after'] = [
      '#type' => 'container',
      '#markup' => "Total number of rows after import: <div>" . $total_after . "</div>",
      '#attributes' => ['class' => ['record_summary']],
    ];
    $form['import_details_section']['summaryset']['summary']['created'] = [
      '#type' => 'container',
      '#markup' => "Created rows after import: <div>" . $total_created . "</div>",
      '#attributes' => ['class' => ['record_summary']],
    ];
    $form['import_details_section']['summaryset']['summary']['updated'] = [
      '#type' => 'container',
      '#markup' => "Updated rows after import: <div>" . $total_updated . "</div>",
      '#attributes' => ['class' => ['record_summary']],
    ];
    $form['import_details_section']['summaryset']['summary']['deleted'] = [
      '#type' => 'container',
      '#markup' => "Deleted rows after import: <div>" . $total_deleted . "</div>",
      '#attributes' => ['class' => ['record_summary']],
    ];

    $view = render(views_embed_view('csc_import_details_view', 'page_1', $id));

    $form['import_details_section']['summaryset']['embedded_view'] = [
      '#type' => 'container',
      '#suffix' => '</div>',
      '#markup' => $view,
      '#attributes' => ['class' => ['col', 'import-summary']],
    ];

    return $form;


  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_results';

  }

  /**
   * {@inheritdoc}
   */
  public function getTitle($id=NULL) {
    $entity = \Drupal::entityTypeManager()->getStorage('asset')->load($id);
    $filename = $entity->csc_file_uploaded->value;
    $date = date('d/m/Y', $entity->csc_time_submitted->value);
    return "Import Results for '{" . $filename . "}' uploaded on {" . $date . "}";
  }

  /**
   * {@inheritdoc}
   */
  public function SubmitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('csc_csv_import.submit_workbooks');
  }

}
