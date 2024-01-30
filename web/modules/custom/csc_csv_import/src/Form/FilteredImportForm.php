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
 * Display a filtered view of an imported content type
 */
class FilteredImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $type = NULL, $id = NULL) {
    $form['#attached']['library'][] = 'csc_csv_import/excel_import_form';
    $form['#attributes']['enctype'][] = 'multipart/form-data';

    $view = render(views_embed_view($type . '_view', 'page_1', $id));

    # Three main sections of the form
    $title = ($type == 'csc_import_history') ? 'Import History' : 'You have successfully imported the following records:';
    $form['title'] = [
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
      '#markup' => $title,
    ];

    $form['viewsection'] = [
      '#type' => 'container',
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#attributes' => ['class' => ['bottom-form']],
    ];

    $form['viewsection']['view'] = [
      '#type' => 'container',
      '#markup' => $view,
    ];

    return $form;

  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'filtered_import';
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle($type=NULL, $id=NULL) {
    $name = "Import Results";
    if ($type == 'csc_import_history') {
      return $name;
    }

    $entity = \Drupal::entityTypeManager()->getStorage('asset')->load($id);
    $filename = $entity->csc_file_uploaded->value;
    $date = date('d/m/Y', $entity->csc_time_submitted->value);

    $data = \Drupal::entityTypeManager()->getStorage('log')->loadByProperties(['type'=>'csc_import_details', 'csc_import_history_reference'=>$id]);

    foreach ($data as $d) {
      if ($type == $d->csc_import_machine_name->value) {
        $name = $d->csc_import_sheetname->value;
      }
    }

    return $name . " for '{" . $filename . "}' uploaded on {" . $date . "}";
  }

  public function SubmitForm(array &$form, FormStateInterface $form_state) {
  }
}
