<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;

/**
 * Lab test method form.
 */
class LabTestMethodForm extends PodsFormBase {

  /**
   * Get taxonomy term options.
   */
  public function getTaxonomyOptions($bundle) {
    $options = $this->entityOptions('taxonomy_term', $bundle);
    foreach ($options as $key => $option) {
      $options[$key] = html_entity_decode($option);
    }
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get asset options.
   */
  private function getAssetOptions($assetType) {
    $options = $this->entityOptions('asset', $assetType);
    return ['' => '- Select -'] + $options;
  }

  /**
   * Convert fraction to decimal.
   */
  private function convertFractionsToDecimal($labTestMethod, $field) {
    $num = $labTestMethod->get($field)[0]->getValue()["numerator"];
    $denom = $labTestMethod->get($field)[0]->getValue()["denominator"];
    return $num / $denom;
  }

  /**
   * Create element names.
   */
  private function createElementNames() {
    return [
      'field_lab_method_name',
      'field_lab_method_project',
      'field_lab_soil_test_laboratory',
      'field_lab_method_aggregate_stability_method',
      'field_lab_method_aggregate_stability_unit',
      'field_lab_method_respiration_incubation_days',
      'field_lab_method_respiration_detection_method',
      'field_lab_method_bulk_density_core_diameter', 
      'field_lab_method_bulk_density_volume',
      'field_lab_method_infiltration_method',
      'field_lab_method_electroconductivity_method',
      'field_lab_method_nitrate_n_method',
      'field_lab_method_soil_ph_method',
      'field_lab_method_phosphorus_method',
      'field_lab_method_potassium_method',
      'field_lab_method_calcium_method',
      'field_lab_method_magnesium_method',
      'field_lab_method_sulfur_method',
      'field_lab_method_iron_method',
      'field_lab_method_manganese_method',
      'field_lab_method_copper_method',
      'field_lab_method_zinc_method',
      'field_lab_method_boron_method',
      'field_lab_method_aluminum_method',
      'field_lab_method_molybdenum_method',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    $labTestMethod = $asset;

    $is_edit = $labTestMethod <> NULL;

    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('lab_test_id', $labTestMethod->id());

    }
    else {
      $form_state->set('operation', 'create');
    }

    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'cig_pods/lab_test_method_admin_form';
    $form['#attached']['library'][] = 'core/drupal.form';
    // Allows getting at the values hierarchy in form state.
    $form['#tree'] = TRUE;

    $agg_stab_unit = $this->getTaxonomyOptions("d_aggregate_stability_un");
    $agg_stab_method = $this->getTaxonomyOptions("d_aggregate_stability_me");
    $infiltration_method = $this->getTaxonomyOptions("d_infiltration_method");
    $ec_method = $this->getTaxonomyOptions("d_ec_method");
    $nitrate_method = $this->getTaxonomyOptions("d_nitrate_n_method");
    $resp_detect = $this->getTaxonomyOptions("d_respiration_detection_");
    $respiration_incubation = $this->getTaxonomyOptions("d_respiration_incubation");
    $s_he_extract = $this->getTaxonomyOptions("d_soil_health_extraction");
    $s_he_test_laboratory = $this->getTaxonomyOptions("d_laboratory");
    $soil_ph_method = $this->getTaxonomyOptions("d_ph_method");

    $project = $this->getAssetOptions('project');
    if (empty($form_state->getValue('field_lab_soil_test_laboratory'))) {
      $selected_family = key($s_he_test_laboratory);
    }
    else {
      $selected_family = $form_state->getValue('field_lab_soil_test_laboratory');
    }
    $form['lab_test_title'] = [
      '#markup' => '<h1>Method</h1>',
    ];

    $form['subform_1'] = [
      '#markup' => '<div class="subform-title-container subform-title-container-top"><h2>Method Information</h2><h4>2 Fields | Section 1 of 2</h4></div>',
    ];

    $method_name_default = $is_edit ? $labTestMethod->get('field_lab_method_name')->value : '';
    $form['field_lab_method_name'] = [
      '#type' => 'textfield',
      '#title' => 'Name',
      '#default_value' => $method_name_default,
      '#required' => TRUE,
      '#validated' => TRUE,
    ];

    $project_default = $is_edit ? $labTestMethod->get('field_lab_method_project')->target_id : NULL;
    $form['field_lab_method_project'] = [
      '#type' => 'select',
      '#title' => 'Project',
      '#options' => $project,
      '#default_value' => $project_default,
      '#required' => TRUE,
      '#validated' => TRUE,
    ];

    $form['lab_form_header'] = [
      '#markup' => '<div class="subtitle-container section1"><h2>Soil Health Test Methods</h2><h4>23 Fields | Section 2 of 2</h4></div>',
    ];

    $lab_default = $is_edit ? $labTestMethod->get('field_lab_soil_test_laboratory')->target_id : NULL;
    $form['field_lab_soil_test_laboratory'] = [
      '#type' => 'select2',
      '#title' => 'Soil Health Test Laboratory',
      '#options' => $s_he_test_laboratory,
      '#default_value' => $lab_default,
      '#required' => TRUE,
    ];

    $aggregate_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_aggregate_stability_method')->target_id : NULL;
    $form['field_lab_method_aggregate_stability_method'] = [
      '#type' => 'select',
      '#title' => 'Aggregate Stability Method',
      '#options' => $agg_stab_method,
      '#default_value' => $aggregate_method_default_value,
      '#required' => TRUE,
    ];

    $aggregate_unit_default_value = $is_edit ? $labTestMethod->get('field_lab_method_aggregate_stability_unit')->target_id : NULL;
    $form['field_lab_method_aggregate_stability_unit'] = [
      '#type' => 'select',
      '#title' => 'Aggregate Stability Unit',
      '#options' => $agg_stab_unit,
      '#default_value' => $aggregate_unit_default_value,
      '#required' => TRUE,
    ];

    $respiratory_incubation_default_value = $is_edit ? $labTestMethod->get('field_lab_method_respiration_incubation_days')->target_id : NULL;
    $form['field_lab_method_respiration_incubation_days'] = [
      '#type' => 'select',
      '#options' => $respiration_incubation,
      '#title' => 'Respiration Incubation Days',
      '#min' => 0,
      '#default_value' => $respiratory_incubation_default_value,
      '#required' => TRUE,
    ];

    $respiratory_detection_default_value = $is_edit ? $labTestMethod->get('field_lab_method_respiration_detection_method')->target_id : NULL;
    $form['field_lab_method_respiration_detection_method'] = [
      '#type' => 'select',
      '#title' => 'Respiration Detection Method',
      '#options' => $resp_detect,
      '#default_value' => $respiratory_detection_default_value,
      '#required' => TRUE,
    ];

    $bulk_density_core_default = $is_edit ? $this->convertFractionsToDecimal($labTestMethod, 'field_lab_method_bulk_density_core_diameter') : NULL;
    $form['field_lab_method_bulk_density_core_diameter'] = [
      '#type' => 'number',
      '#title' => $this->t('Bulk Density Core Diameter (Unit Inches)'),
      '#step' => 0.01,
      '#min' => 0,
      '#default_value' => $bulk_density_core_default,
      '#required' => TRUE,
    ];

    $bulk_density_volume_default = $is_edit ? $this->convertFractionsToDecimal($labTestMethod, 'field_lab_method_bulk_density_volume') : NULL;
    $form['field_lab_method_bulk_density_volume'] = [
      '#type' => 'number',
      '#step' => 0.01,
      '#min' => 0,
      '#title' => $this->t('Bulk Density Volume (Cubic Centimeters)'),
      '#default_value' => $bulk_density_volume_default,
      '#required' => TRUE,
    ];

    $infiltration_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_infiltration_method')->target_id : NULL;
    $form['field_lab_method_infiltration_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Infiltration Method'),
      '#options' => $infiltration_method,
      '#default_value' => $infiltration_method_default_value,
      '#required' => TRUE,
    ];

    $electroconductivity_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_electroconductivity_method')->target_id : NULL;
    $form['field_lab_method_electroconductivity_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Electroconductivity Method'),
      '#options' => $ec_method,
      '#default_value' => $electroconductivity_method_default_value,
      '#required' => TRUE,
    ];

    $nitrate_n_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_nitrate_n_method')->target_id : NULL;
    $form['field_lab_method_nitrate_n_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Nitrate-N Method'),
      '#options' => $nitrate_method,
      '#default_value' => $nitrate_n_method_default_value,
      '#required' => TRUE,
    ];
    
    $soil_ph_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_soil_ph_method')->target_id : NULL;
    $form['field_lab_method_soil_ph_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Soil pH Method'),
      '#options' => $soil_ph_method,
      '#default_value' => $soil_ph_method_default_value,
      '#required' => TRUE,
    ];

    $phosphorus_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_phosphorus_method')->target_id : NULL;
    $form['field_lab_method_phosphorus_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Phosphorus Method'),
      '#options' => $s_he_extract,
      '#default_value' => $phosphorus_method_default_value,
      '#required' => TRUE,
    ];

    $potassium_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_potassium_method')->target_id : NULL;
    $form['field_lab_method_potassium_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Potassium Method'),
      '#options' => $s_he_extract,
      '#default_value' => $potassium_method_default_value,
      '#required' => TRUE,
    ];

    $calcium_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_calcium_method')->target_id : NULL;
    $form['field_lab_method_calcium_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Calcium Method'),
      '#options' => $s_he_extract,
      '#default_value' => $calcium_method_default_value,
      '#required' => TRUE,
    ];

    $magnesium_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_magnesium_method')->target_id : NULL;
    $form['field_lab_method_magnesium_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Magnesium Method'),
      '#options' => $s_he_extract,
      '#default_value' => $magnesium_method_default_value,
      '#required' => TRUE,
    ];

    $sulfur_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_sulfur_method')->target_id : NULL;
    $form['field_lab_method_sulfur_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Sulfur Method'),
      '#options' => $s_he_extract,
      '#default_value' => $sulfur_method_default_value,
      '#required' => TRUE,
    ];

    $iron_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_iron_method')->target_id : NULL;
    $form['field_lab_method_iron_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Iron Method'),
      '#options' => $s_he_extract,
      '#default_value' => $iron_method_default_value,
      '#required' => TRUE,
    ];

    $manganese_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_manganese_method')->target_id : NULL;
    $for['field_lab_method_manganese_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Manganese Method'),
      '#options' => $s_he_extract,
      '#default_value' => $manganese_method_default_value,
      '#required' => TRUE,
    ];

    $copper_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_copper_method')->target_id : NULL;
    $form['field_lab_method_copper_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Copper Method'),
      '#options' => $s_he_extract,
      '#default_value' => $copper_method_default_value,
      '#required' => TRUE,
    ];

    $zinc_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_zinc_method')->target_id : NULL;
    $form['field_lab_method_zinc_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Zinc Method'),
      '#options' => $s_he_extract,
      '#default_value' => $zinc_method_default_value,
      '#required' => TRUE,
    ];

    $boron_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_boron_method')->target_id : NULL;
    $form['field_lab_method_boron_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Boron Method'),
      '#options' => $s_he_extract,
      '#default_value' => $boron_method_default_value,
      '#required' => TRUE,
    ];

    $aluminum_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_aluminum_method')->target_id : NULL;
    $form['field_lab_method_aluminum_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Aluminum Method'),
      '#options' => $s_he_extract,
      '#default_value' => $aluminum_method_default_value,
      '#required' => TRUE,
    ];

    $molybdenum_method_default_value = $is_edit ? $labTestMethod->get('field_lab_method_molybdenum_method')->target_id : NULL;
    $form['field_lab_method_molybdenum_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Molybdenum Method'),
      '#options' => $s_he_extract,
      '#default_value' => $molybdenum_method_default_value,
      '#required' => TRUE,
    ];


	$asset_id = $is_edit ? $asset->id() : NULL;

	$form['asset_id'] = [
	  '#type' => 'hidden',
	  '#value' => $asset_id,
	  '#attributes' => ['id' => ['asset_id'],],
	];

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#limit_validation_errors' => '',
      '#value' => $this->t('Cancel'),
      '#submit' => ['::redirectAfterCancel'],
    ];

    if ($is_edit) {
      $form['actions']['delete'] = [
        '#type' => 'submit',
        '#value' => $this->t('Delete'),
        '#submit' => ['::deleteLabTest'],
      ];
    }

    return $form;
  }

  /**
   * Redirect after cancel.
   */
  public function redirectAfterCancel(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('cig_pods.dashboard');
  }

  /**
   * Delete lab test.
   */
  public function deleteLabTest(array &$form, FormStateInterface $form_state) {

    // @todo we probably want a confirm stage on the delete button. Implementations exist online
    $lab_test_id = $form_state->get('lab_test_id');
    $labTest = \Drupal::entityTypeManager()->getStorage('asset')->load($lab_test_id);

    try {
      $labTest->delete();
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
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'lab_test_methods_admin';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $method_submission = [];
    if ($form_state->get('operation') === 'create') {
      $elementNames = $this->createElementNames();
      foreach ($elementNames as $elemName) {
        $method_submission[$elemName] = $form_state->getValue($elemName);
      }

      $method_submission['name'] = $method_submission['field_lab_method_name'];

      $method_submission['type'] = 'lab_testing_method';
      $method = Asset::create($method_submission);
      $method->save();

      $this->setAwardReference($method, $method->get('field_lab_method_project')->target_id);

      $form_state->setRedirect('cig_pods.dashboard');

    }
    else {
      $id = $form_state->get('lab_test_id');
      $labTestMethod = \Drupal::entityTypeManager()->getStorage('asset')->load($id);
      $elementNames = $this->createElementNames();
      foreach ($elementNames as $elemName) {
        $labTestMethod->set($elemName, $form_state->getValue($elemName));
      }

      $labTestMethod->set('name', $labTestMethod->get('field_lab_method_name')->value);

      $labTestMethod->save();

      $this->setAwardReference($labTestMethod, $labTestMethod->get('field_lab_method_project')->target_id);

      $form_state->setRedirect('cig_pods.dashboard');
    }
  }

  /**
   * Set award reference.
   */
  public function setAwardReference($assetReference, $projectReference) {
    $project = \Drupal::entityTypeManager()->getStorage('asset')->load($projectReference);
    $award = \Drupal::entityTypeManager()->getStorage('asset')->load($project->get('award')->target_id);
    $assetReference->set('award', $award);
    $assetReference->save();
  }

}
