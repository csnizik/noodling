<?php

namespace Drupal\csc_csv_import\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\Http\MultipartFormDataRequest;
use Symfony\Component\HttpFoundtion\Request;
use Symfony\Component\HttpFoundtion\Response;
use Drupal\Core\Url;

/**
 * Excel Import Form.
 */
class WorkbookDateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    
    $form['#attached']['library'][] = 'csc_csv_import/excel_import_form';
    $form['#attributes']['enctype'][] = 'multipart/form-data';

    $quarter_options = [
      'January 1 - March 31', 
      'April 1 - June 30',
      'July 1 - September 30',
      'October 1 - December 31',
    ];

    $year_options = [
      '2022', 
      '2023',
    ];

    $form['instructions'] = [
      '#prefix' => '<p class="instruction-text">',
      '#suffix' => '<p>',
      '#markup' => 'Please upload both your <b>project reporting workbook</b> and <b>supplemental reporting workbook</b> here.<br>
                    Both workbooks should be imported using the form below as separate files. This will require two separate imports.<br>
                    Our system will parse your imported workbook and automatically categorize it as either "Main" or "Supplemental".',
    ];

    $form['year'] = [
      '#type' => 'select',
      '#title' =>'Choose Year of Reporting',
      '#empty_option' => $this->t('Select'),
      '#value' => '',
      '#required' => TRUE,
      '#options' => $year_options,
    ];

    $form['quarter'] = [
      '#type' => 'select',
      '#title' =>'Choose Months of Reporting',
      '#empty_option' => $this->t('Select'),
      '#value' => '',
      '#required' => TRUE,
      '#options' => $quarter_options,
    ];

    $form['file'] = [
      '#type' => 'file',
      '#title' =>'Add a single workbook',
      '#description' =>'Accepted File: .xls, .xlxs under 25MB',
      '#required' => TRUE,
      '#upload_validators' => [
        '#file_validate_extensions' => ['xls xlsx'],
      ],
    ];  
      

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];

    $main_path = "/modules/custom/cig_pods_csc/static/PCSC_Project_Reporting_Workbook.xlsx";
    $supp_path = "/modules/custom/cig_pods_csc/static/PCSC_Supplemental_Reporting_Workbook.xlsx";
    $svg = '<svg class="fsa-icon fsa-icon--size-4" aria-hidden="true" focusable="false" role="img" fill="#494440" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"></path></svg>';
    $link_markup = '<span id="link-header"><b>Download Workbook Templates</b></span>';
    $link_markup .= $svg . '<a class="downloads" href="' . $main_path . '" download>Partnerships for Climate-Smart Commodities Project Reporting Workbook</a><br>';
    $link_markup .= $svg . '<a class="downloads" href="' . $supp_path . '" download>Partnerships for Climate-Smart Commodities Supplemental Reporting Workbook</a>';
    $link_markup = Markup::create($link_markup);

    $form['links'] = [
      '#prefix' => '<hr><p class="workbook-links">',
      '#suffix' => '<p></div>',
      '#markup' => $link_markup,
    ];

    $form['import_history_section'] = [
      '#type' => 'container',
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#attributes' => ['class' => ['bottom-form']],
    ];

    $view = render(views_embed_view('csc_import_history_embedded_view', 'page_1'));

    $view_link = Url::fromRoute('csc_csv_import.filtered_import', ['type' => 'csc_import_history', 'id' => 1])->toString();

    $view_header = '<div class="col-sm">
                      <h2 class="view-title">Import History</h2>
                    </div>
                    <div class="col-sm view-link-col">
                      <a class="view-link" href=' . $view_link . '>View All Import History</a>
                    </div>';

    $form['import_history_section']['header'] = [
      '#prefix' => '<div class="container view-header">',
      '#suffix' => '</div>',
      '#markup' => $view_header,
    ];

    $form['import_history_section']['view'] = [
      '#type' => 'container',
      '#suffix' => '</div>',
      '#markup' => $view,
    ];

    $form['#action'] = \Drupal\Core\Url::fromRoute('csc_csv_import.process_workbook')->toString();

    return $form;


  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'excel-import-form';

  }

  /**
   * {@inheritdoc}
   */
  public function SubmitForm(array &$form, FormStateInterface $form_state) {

  }

}
