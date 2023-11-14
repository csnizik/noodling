<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;

/**
 * Lab results form.
 */
class LabResultsForm extends PodsFormBase {

  /**
   * Get taxonomy term options.
   */
  public function getLabInterpretationOptions($bundle) {
    $options = $this->entityOptions('taxonomy_term', $bundle);
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get soil sample options.
   */
  private function getSoilSampleOptions() {
    $options = $this->entityOptions('asset', 'soil_health_sample');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get lab method options.
   */
  private function getLabMethodOptions() {
    $options = $this->entityOptions('asset', 'lab_testing_method');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Convert Fraction to decimal.
   */
  private function convertFractionsToDecimal($is_edit, $labResults, $field) {
    try{
      $get_field = $labResults->get($field)[0]->getValue();
    }catch(\Throwable $t){
      return '';
    }
    if ($is_edit) {
      $num = $labResults->get($field)[0]->getValue()["numerator"];
      $denom = $labResults->get($field)[0]->getValue()["denominator"];
      return $num / $denom;
    }
    else {
      return "";
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    $labResults = $asset;

    $is_edit = $labResults <> NULL;

    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('lab_result_id', $labResults->id());
    }
    else {
      $form_state->set('operation', 'create');
    }

    $form['#attached']['library'][] = 'cig_pods/lab_results_form';
    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'core/drupal.form';

    $lab_interpretation = $this->getLabInterpretationOptions("d_lab_interpretation");

    $soil_sample = $this->getSoilSampleOptions();

    $lab_method = $this->getLabMethodOptions();

    $form['title'] = [
      '#markup' => '<h1>Soil Test Results</h1',
    ];

    $form['sub_title_1'] = [
      '#markup' => '<div class="subform-title-container raw-values-subheading"><h2>Test Information </h2><h4>2 Fields | Section 1 of 4</h4></div>',
    ];

    $soil_sample_default_id = $is_edit ? $labResults->get('field_lab_result_soil_sample')->target_id : NULL;
    $form['field_lab_result_soil_sample'] = [
      '#type' => 'select',
      '#title' => $this->t('Soil Sample ID'),
      '#options' => $soil_sample,
      '#default_value' => $soil_sample_default_id,
      '#required' => TRUE,
    ];

    $lab_method_default = $is_edit ? $labResults->get('field_lab_result_method')->target_id : NULL;
    $form['field_lab_result_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Lab Method'),
      '#options' => $lab_method,
      '#default_value' => $lab_method_default,
      '#required' => TRUE,
    ];

    $form['sub_title_2'] = [
      '#markup' => '<div class="subform-title-container raw-values-subheading"><h2>Soil Health Raw Values</h2><h4>5 Fields | Section 2 of 4</h4></div>',
    ];

    $organic_carbon_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_raw_soil_organic_carbon');
    $form['field_lab_result_raw_soil_organic_carbon'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Soil Organic Carbon (Unit Percent)'),
      '#description' => '',
      '#default_value' => $organic_carbon_results,
      '#required' => FALSE,
    ];

    $aggregate_stability_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_raw_aggregate_stability');
    $form['field_lab_result_raw_aggregate_stability'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Aggregate Stability (Unit Percent)'),
      '#description' => '',
      '#default_value' => $aggregate_stability_results,
      '#required' => FALSE,
    ];

    $raw_respiration_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_raw_respiration');
    $form['field_lab_result_raw_respiration'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Respiration (Unit mg/g dry weight)'),
      '#description' => '',
      '#default_value' => $raw_respiration_results,
      '#required' => FALSE,
    ];

    $active_carbon_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_active_carbon');
    $form['field_lab_result_active_carbon'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Active Carbon (Unit ppm)'),
      '#description' => '',
      '#default_value' => $active_carbon_results,
      '#required' => FALSE,
    ];

    $organic_nitrogen_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_available_organic_nitrogen');
    $form['field_lab_result_available_organic_nitrogen'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Available Organic Nitrogen (ACE Protein (Unit ppm))'),
      '#description' => '',
      '#default_value' => $organic_nitrogen_results,
      '#required' => FALSE,
    ];

    $form['subform_2'] = [
      '#markup' => '<div class="subform-title-container function-subheading"><h2>Soil Function</h2><h4>2 Fields | Section 3 of 4</h4></div>',
    ];

    $bulk_density_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_bulk_density_dry_weight');
    $form['field_lab_result_sf_bulk_density_dry_weight'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Bulk Density Dry Weight (Unit grams)'),
      '#description' => '',
      '#default_value' => $bulk_density_results,
      '#required' => FALSE,
    ];

    $infiltration_rate_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_infiltration_rate');
    $form['field_lab_result_sf_infiltration_rate'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Infiltration Rate (inches Per Hour)'),
      '#description' => '',
      '#default_value' => $infiltration_rate_results,
      '#required' => FALSE,
    ];

    $form['subform_3'] = [
      '#markup' => '<div class="subform-title-container fertility-subheading"><h2>Soil Fertility</h2><h4>31 Fields | Section 4 of 4</h4></div>',
    ];

    $ph_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_ph_value');
    $form['field_lab_result_sf_ph_value'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('pH (Decimal value between 1 and 14 to the tenth)'),
      '#description' => '',
      '#default_value' => $ph_results,
      '#required' => FALSE,
    ];

    $electroconductivity_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_electroconductivity');
    $form['field_lab_result_sf_electroconductivity'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Electroconductivity (EC (Unit dS/m))'),
      '#description' => '',
      '#default_value' => $electroconductivity_results,
      '#required' => FALSE,
    ];

    $ec_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_ec_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_ec_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Electroconductivity Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $ec_interp_results,
      '#required' => FALSE,
    ];

    $cation_exchanges_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_cation_exchange_capacity');
    $form['field_lab_result_sf_cation_exchange_capacity'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Cation Exchange Capacity (CEC (Unit ppm))'),
      '#description' => '',
      '#default_value' => $cation_exchanges_results,
      '#required' => FALSE,
    ];

    $nitrate_n_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_nitrate_n');
    $form['field_lab_result_sf_nitrate_n'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Nitrate-N (Unit ppm)'),
      '#description' => '',
      '#default_value' => $nitrate_n_results,
      '#required' => FALSE,
    ];

    $nitrate_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_nitrate_n_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_nitrate_n_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Nitrate-N Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $nitrate_interp_results,
      '#required' => FALSE,
    ];

    $nitrogen_dry_combustion_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_nitrogen_by_dry_combustion');
    $form['field_lab_result_sf_nitrogen_by_dry_combustion'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Total Nitrogen by Dry Combustion (Unit Percent)'),
      '#description' => '',
      '#default_value' => $nitrogen_dry_combustion_results,
      '#required' => FALSE,
    ];

    $phosphorus_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_phosphorous');
    $form['field_lab_result_sf_phosphorous'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Phosphorus (Unit ppm)'),
      '#description' => '',
      '#default_value' => $phosphorus_results,
      '#required' => FALSE,
    ];

    $phosphorus_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_phosphorous_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_phosphorous_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Phosphorus Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $phosphorus_interp_results,
      '#required' => FALSE,
    ];

    $potassium_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_potassium');
    $form['field_lab_result_sf_potassium'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Potassium (Unit ppm)'),
      '#description' => '',
      '#default_value' => $potassium_results,
      '#required' => FALSE,
    ];

    $potassium_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_potassium_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_potassium_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Potassium Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $potassium_interp_results,
      '#required' => FALSE,
    ];

    $calcium_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_calcium');
    $form['field_lab_result_sf_calcium'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Calcium (Unit ppm)'),
      '#description' => '',
      '#default_value' => $calcium_results,
      '#required' => FALSE,
    ];

    $calcium_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_calcium_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_calcium_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Calcium Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $calcium_interp_results,
      '#required' => FALSE,
    ];

    $magnesium_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_magnesium');
    $form['field_lab_result_sf_magnesium'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Magnesium (Unit ppm)'),
      '#description' => '',
      '#default_value' => $magnesium_results,
      '#required' => FALSE,
    ];

    $magnesium_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_magnesium_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_magnesium_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Magnesium Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $magnesium_interp_results,
      '#required' => FALSE,
    ];

    $sulfur_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_sulfur');
    $form['field_lab_result_sf_sulfur'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Sulfur (Unit ppm)'),
      '#description' => '',
      '#default_value' => $sulfur_results,
      '#required' => FALSE,
    ];

    $sulfur_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_sulfur_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_sulfur_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Sulfur Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $sulfur_interp_results,
      '#required' => FALSE,
    ];

    $iron_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_iron');
    $form['field_lab_result_sf_iron'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Iron (Unit ppm)'),
      '#description' => '',
      '#default_value' => $iron_results,
      '#required' => FALSE,
    ];

    $iron_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_iron_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_iron_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Iron Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $iron_interp_results,
      '#required' => FALSE,
    ];

    $manganese_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_manganese');
    $form['field_lab_result_sf_manganese'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Manganese (Unit ppm)'),
      '#description' => '',
      '#default_value' => $manganese_results,
      '#required' => FALSE,
    ];

    $manganese_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_manganese_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_manganese_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Manganese Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $manganese_interp_results,
      '#required' => FALSE,
    ];

    $copper_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_copper');
    $form['field_lab_result_sf_copper'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Copper (Unit ppm)'),
      '#description' => '',
      '#default_value' => $copper_results,
      '#required' => FALSE,
    ];

    $copper_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_copper_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_copper_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Copper Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $copper_interp_results,
      '#required' => FALSE,
    ];

    $zinc_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_zinc');
    $form['field_lab_result_sf_zinc'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Zinc (Unit ppm)'),
      '#description' => '',
      '#default_value' => $zinc_results,
      '#required' => FALSE,
    ];

    $zinc_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_zinc_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_zinc_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Zinc Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $zinc_interp_results,
      '#required' => FALSE,
    ];

    $boron_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_boron');
    $form['field_lab_result_sf_boron'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Boron (Unit ppm)'),
      '#description' => '',
      '#default_value' => $boron_results,
      '#required' => FALSE,
    ];

    $boron_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_boron_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_boron_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Boron Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $boron_interp_results,
      '#required' => FALSE,
    ];

    $aluminum_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_aluminum');
    $form['field_lab_result_sf_aluminum'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Aluminum (Unit ppm)'),
      '#description' => '',
      '#default_value' => $aluminum_results,
      '#required' => FALSE,
    ];

    $aluminum_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_aluminum_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_aluminum_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Aluminum Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $aluminum_interp_results,
      '#required' => FALSE,
    ];

    $molybdenum_results = $this->convertFractionsToDecimal($is_edit, $labResults, 'field_lab_result_sf_molybdenum');
    $form['field_lab_result_sf_molybdenum'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.1,
      '#title' => $this->t('Molybdenum (Unit ppm)'),
      '#description' => '',
      '#default_value' => $molybdenum_results,
      '#required' => FALSE,
    ];

    $molybdenum_interp_results = $is_edit ? $labResults->get('field_lab_result_sf_molybdenum_lab_interpretation')->target_id : NULL;
    $form['field_lab_result_sf_molybdenum_lab_interpretation'] = [
      '#type' => 'select',
      '#title' => $this->t('Molybdenum Lab Interpretation'),
      '#options' => $lab_interpretation,
      '#default_value' => $molybdenum_interp_results,
      '#required' => FALSE,
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
      '#value' => $this->t('Cancel'),
      '#limit_validation_errors' => '',
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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'lab_results_form';
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
    try {
      $lab_result_id = $form_state->get('lab_result_id');
      $labTest = \Drupal::entityTypeManager()->getStorage('asset')->load($lab_result_id);

      $labTest->delete();
      $form_state->setRedirect('cig_pods.dashboard');
    }
    catch (\Exception $e) {
      $this
        ->messenger()
        ->addStatus($this
          ->t('This item cannot be deleted right now because its information is being referenced elsewhere.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $elementNames = array_keys($form_state->getValues());
    $profile_submission = [];
    if ($form_state->get('operation') === 'create') {
      foreach ($elementNames as $elemName) {
        if (strpos($elemName, "field_") === 0) {
          $profile_submission[$elemName] = $form_state->getValue($elemName);
        }
      }
      
      $profile_submission['name'] = 'Soil Test Results';
      $profile_submission['type'] = 'lab_result';
      $profile = Asset::create($profile_submission);
      // $profile->save();
      
      $this->setAwardReference($profile, $profile->get('field_lab_result_soil_sample')->target_id);

      $form_state->setRedirect('cig_pods.dashboard');

    }
    else {
      $id = $form_state->get('lab_result_id');
      $labTestProfile = \Drupal::entityTypeManager()->getStorage('asset')->load($id);
      foreach ($elementNames as $elemName) {
        if (strpos($elemName, "field_") === 0) {
          $labTestProfile->set($elemName, $form_state->getValue($elemName));
        }
      }

      $labTestProfile->set('name', 'Soil Test Results');

      $labTestProfile->save();

      $this->setAwardReference($labTestProfile, $labTestProfile->get('field_lab_result_soil_sample')->target_id);

      $form_state->setRedirect('cig_pods.dashboard');
    }
  }

  /**
   * Set award reference.
   */
  public function setAwardReference($assetReference, $sampleReference) {
    $soilSample = \Drupal::entityTypeManager()->getStorage('asset')->load($sampleReference);
    $award = \Drupal::entityTypeManager()->getStorage('asset')->load($soilSample->get('award')->target_id);
    $assetReference->set('award', $award);
    $assetReference->save();
  }

}
