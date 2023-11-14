<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;

/**
 * Field assessment form.
 */
class FieldAssessmentForm extends PodsFormBase {

  /**
   * Get SHMU options.
   */
  public function getShmuOptions() {
    $options = $this->entityOptions('asset', 'soil_health_management_unit');
    return ['' => '- Select -'] + $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    $assessment = $asset;
    $form['#attached']['library'][] = 'cig_pods/field_assessment_form';
    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'core/drupal.form';

    $is_edit = $assessment <> NULL;

    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('assessment_id', $assessment->id());

    }
    else {
      $form_state->set('operation', 'create');
    }

    if ($form_state->get('calculate_rcs') == NULL) {
      $form_state->set('calculate_rcs', FALSE);
    }

    // No hierarchy needed for this form.
    $form['#tree'] = FALSE;

    $form['producer_title'] = [
      '#markup' => '<h1>Cropland In-Field Soil Health (CIFSH) Assessment</h1>',
    ];

    $form['subform_1'] = [
      '#markup' => '<div class="subform-title-container subform1-spacer"><h2>CIFSH Information</h2><h4>13 Fields | Section 1 of 1</h4></div>',
    ];

    $shmu_value = $is_edit ? $assessment->get('shmu')->target_id : '';
    $form['shmu'] = [
      '#type' => 'select',
      '#title' => 'Select a Soil Health Management Unit',
      '#options' => $this->getShmuOptions(),
      '#default_value' => $shmu_value,
      '#required' => TRUE,
    ];

    // Date field requires some special handling.
    if ($is_edit) {
      $date_value = $assessment->get('field_assessment_date')->value;
      $field_timestamp_default_value = date("Y-m-d", $date_value);
    }
    else {
      $field_timestamp_default_value = '';
    }
    $form['field_assessment_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date'),
      '#description' => '',
      '#default_value' => $field_timestamp_default_value,
      '#required' => TRUE,
    ];

    $form['assessment_wrapper'] = [
      '#prefix' => '<div id="assessment_wrapper">',
      '#suffix' => '</div>',
    ];

    $field_assessment_soil_cover_value = $is_edit ? $assessment->get('field_assessment_soil_cover')->value : '';

    $assessment_evaluations_options = [
      '' => '- Select -',
      0 => 'Yes',
      1 => 'No',
      2 => 'N/A',
    ];

    $form['assessment_wrapper']['field_assessment_soil_cover'] = [
      '#type' => 'select',
      '#title' => $this->t('Soil Cover'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_soil_cover_value,
      '#required' => FALSE,
    ];

    $field_assessment_residue_breakdown_value = $is_edit ? $assessment->get('field_assessment_residue_breakdown')->value : '';

    $form['assessment_wrapper']['field_assessment_residue_breakdown'] = [
      '#type' => 'select',
      '#title' => $this->t('Residue Breakdown'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_residue_breakdown_value,
      '#required' => FALSE,
    ];

    $field_assessment_surface_crusts_value = $is_edit ? $assessment->get('field_assessment_surface_crusts')->value : '';

    $form['assessment_wrapper']['field_assessment_surface_crusts'] = [
      '#type' => 'select',
      '#title' => $this->t('Surface Crusts'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_surface_crusts_value,
      '#required' => FALSE,
    ];
    $field_assessment_ponding_value = $is_edit ? $assessment->get('field_assessment_ponding')->value : '';

    $form['assessment_wrapper']['field_assessment_ponding'] = [
      '#type' => 'select',
      '#title' => $this->t('Ponding'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_ponding_value,
      '#required' => FALSE,
    ];

    $field_assessment_penetration_resistance_value = $is_edit ? $assessment->get('field_assessment_penetration_resistance')->value : '';

    $form['assessment_wrapper']['field_assessment_penetration_resistance'] = [
      '#type' => 'select',
      '#title' => $this->t('Penetration Resistance'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_penetration_resistance_value,
      '#required' => FALSE,
    ];
    $field_assessment_water_stable_aggregates_value = $is_edit ? $assessment->get('field_assessment_water_stable_aggregates')->value : '';

    $form['assessment_wrapper']['field_assessment_water_stable_aggregates'] = [
      '#type' => 'select',
      '#title' => $this->t('Water Stable Aggregates'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_water_stable_aggregates_value,
      '#required' => FALSE,
    ];

    $field_assessment_soil_structure_value = $is_edit ? $assessment->get('field_assessment_soil_structure')->value : '';

    $form['assessment_wrapper']['field_assessment_soil_structure'] = [
      '#type' => 'select',
      '#title' => $this->t('Soil Structure'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_soil_structure_value,
      '#required' => FALSE,
    ];

    $field_assessment_soil_color_value = $is_edit ? $assessment->get('field_assessment_soil_color')->value : '';

    $form['assessment_wrapper']['field_assessment_soil_color'] = [
      '#type' => 'select',
      '#title' => $this->t('Soil Color'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_soil_color_value,
      '#required' => FALSE,
    ];

    $field_assessment_plant_roots_value = $is_edit ? $assessment->get('field_assessment_plant_roots')->value : '';

    $form['assessment_wrapper']['field_assessment_plant_roots'] = [
      '#type' => 'select',
      '#title' => $this->t('Plant Roots'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_plant_roots_value,
      '#required' => FALSE,
    ];

    $field_assessment_biological_diversity_value = $is_edit ? $assessment->get('field_assessment_biological_diversity')->value : '';

    $form['assessment_wrapper']['field_assessment_biological_diversity'] = [
      '#type' => 'select',
      '#title' => $this->t('Biological Diversity'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_biological_diversity_value,
      '#required' => FALSE,
    ];
    $field_assessment_biopores_value = $is_edit ? $assessment->get('field_assessment_biopores')->value : '';

    $form['assessment_wrapper']['field_assessment_biopores'] = [
      '#type' => 'select',
      '#title' => $this->t('Biopores'),
      '#options' => $assessment_evaluations_options,
      '#default_value' => $field_assessment_biopores_value,
      '#required' => FALSE,
    ];

    $form['assessment_wrapper']['actions']['identify-resource-concerns'] = [
      '#type' => 'submit',
      '#submit' => ['::calcuateResourceConcerns'],
      '#ajax' => [
        'callback' => '::calcuateResourceConcernsCallback',
        'wrapper' => 'assessment_wrapper',
      ],
      '#attributes' => [
        'class' => ['identify-resource-concerns-button'],
      ],
      '#value' => $this->t('Identify Resource Concerns'),
    ];

    if ($form_state->get('calculate_rcs')) {
      $form['assessment_wrapper']['resource_concerns_subheading'] = [
        '#markup' => $this->t('<h2 class="resource-concerns-spacer">Resource Concerns Identified from In-Field Assessment </h2>'),
      ];

      // Invariant: If calculate RCS is True, then all ***_rc_present vars will
      // have a value.
      $soil_organic_matter_rc = $form_state->get('soil_organic_matter_rc_present') ? 'Present' : 'Not Present';

      $form['assessment_wrapper']['organic_matter_title'] = [
        '#markup' => $this->t('<span>Soil Organic Matter Depletion Resource Concern</span> <span class="grey-note">(Calculated from in-field assessment)</span>'),
        '#prefix' => '<div class="calculated_field_container">',
      ];

      $form['assessment_wrapper']['field_assessment_rc_soil_organic_matter'] = [
        '#markup' => $this->t('<div>@organic_matter_assessment</div>', ['@organic_matter_assessment' => $soil_organic_matter_rc]),
        '#suffix' => '</div>',
      ];

      $agg_instability_val = $form_state->get('aggregate_instability_rc_present') ? 'Present' : 'Not Present';

      $form['assessment_wrapper']['agg_instability_title'] = [
        '#markup' => $this->t('<span>Aggregate Instability Resource Concern</span> <span class="grey-note">(Calculated from in-field assessment)</span>'),
        '#prefix' => '<div class="calculated_field_container">',
      ];

      $form['assessment_wrapper']['field_assessment_rc_aggregate_instability'] = [
        '#markup' => $this->t('<div>@agg_instability_assessment</div>', ['@agg_instability_assessment' => $agg_instability_val]),
        '#suffix' => '</div>',
      ];

      $compaction_val = $form_state->get('compaction_rc_present') ? 'Present' : 'Not Present';

      $form['assessment_wrapper']['compaction_title'] = [
        '#markup' => $this->t('<span>Compaction Resource Concern</span> <span class="grey-note">(Calculated from in-field assessment)</span>'),
        '#prefix' => '<div class="calculated_field_container">',
      ];

      $form['assessment_wrapper']['field_assessment_rc_compaction'] = [
        '#markup' => $this->t('<div>@compaction_assessment</div>', ['@compaction_assessment' => $compaction_val]),
        '#suffix' => '</div>',
      ];

      $cfsoh_val = $form_state->get('soil_organism_habitat_rc_present') ? 'Present' : 'Not Present';

      $form['assessment_wrapper']['organism_title'] = [
        '#markup' => $this->t('<span>Soil Organism Habitat Resource Concern</span> <span class="grey-note">(Calculated from in-field assessment)</span>'),
        '#prefix' => '<div class="calculated_field_container">',
      ];

      $form['assessment_wrapper']['field_assessment_rc_soil_organism_habitat'] = [
        '#markup' => $this->t('<div>@organism_assessment</div>', ['@organism_assessment' => $cfsoh_val]),
        '#suffix' => '</div>',
      ];
    }

	$asset_id = $is_edit ? $asset->id() : NULL;

	$form['asset_id'] = [
	  '#type' => 'hidden',
	  '#value' => $asset_id,
	  '#attributes' => ['id' => ['asset_id'],],
	];

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => 'Save',
    ];
    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#submit' => ['::dashboardRedirect'],
      '#limit_validation_errors' => [],

    ];

    if ($is_edit) {
      $form['actions']['delete'] = [
        '#type' => 'submit',
        '#value' => $this->t('Delete'),
        '#submit' => ['::deleteFieldAssessment'],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $date_timestamp = strtotime($form_state->getValue('field_assessment_date'));
    $current_timestamp = strtotime(date('Y-m-d', \Drupal::time()->getCurrentTime()));
    if ($date_timestamp > $current_timestamp) {
      $form_state->setError($form, 'Error: Invalid Date');
    }
  }

  /**
   * Redirect to PODS dashboard.
   */
  public function dashboardRedirect(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('cig_pods.dashboard');
  }

  /**
   * Delete field assessment.
   */
  public function deleteFieldAssessment(array &$form, FormStateInterface $form_state) {

    $assessment_id = $form_state->get('assessment_id');
    $labTest = \Drupal::entityTypeManager()->getStorage('asset')->load($assessment_id);
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $ignored_fields = ['send',
      'form_build_id',
      'form_token',
      'form_id',
      'op',
      'actions',
      'assessment_wrapper',
      'save',
      'cancel',
      'identify-resource-concerns',
      'delete',
      'asset_id',
    ];

    $calculated_fields = [
      'field_assessment_rc_soil_organic_matter',
      'field_assessment_rc_aggregate_instability',
      'field_assessment_rc_compaction',
      'field_assessment_rc_soil_organism_habitat',
    ];

    $date_fields = ['field_assessment_date'];

    $is_edit = $form_state->get('operation') == 'edit';

    if ($is_edit) {
      $id = $form_state->get('assessment_id');
      $assessment = Asset::load($id);
    }
    else {
      $assessment_template = [];
      $assessment_template['type'] = 'field_assessment';
      $assessment = Asset::create($assessment_template);
    }

    $form_values = $form_state->getValues();

    $assessment->set('name', "CIFSH Assessment");

    foreach ($form_values as $key => $value) {
      if (in_array($key, $ignored_fields)) {
        continue;
      }
      if (in_array($key, $date_fields)) {
        // $value is expected to be a string of format yyyy-mm-dd
        $assessment->set($key, strtotime($value));
        continue;
      }
      // Handled outside of loop.
      if (in_array($key, $calculated_fields)) {
        continue;
      }

      $assessment->set($key, $value);
    }

    // Calculated fields.
    $assessment->set('field_assessment_rc_soil_organic_matter', $form_state->get('soil_organic_matter_rc_present'));
    $assessment->set('field_assessment_rc_aggregate_instability', $form_state->get('aggregate_instability_rc_present'));
    $assessment->set('field_assessment_rc_compaction', $form_state->get('compaction_rc_present'));
    $assessment->set('field_assessment_rc_soil_organism_habitat', $form_state->get('soil_organism_habitat_rc_present'));

    $assessment->save();

    $this->setAwardReference($assessment, $assessment->get('shmu')->target_id);

    $form_state->setRedirect('cig_pods.dashboard');
  }

  /**
   * Set award reference.
   */
  public function setAwardReference($assetReference, $shmuReference) {
    $shmu = \Drupal::entityTypeManager()->getStorage('asset')->load($shmuReference);
    $award = \Drupal::entityTypeManager()->getStorage('asset')->load($shmu->get('award')->target_id);
    $assetReference->set('award', $award);
    $assetReference->save();
  }

  /**
   * Calculate resource concerns.
   */
  public function calcuateResourceConcerns(array &$form, FormStateInterface $form_state) {

    $form_values = $form_state->getValues();

    // List of fields in consideration for calculating the presence of
    // compaction.
    $compaction_keys = [
      'field_assessment_ponding',
      'field_assessment_penetration_resistance',
      'field_assessment_water_stable_aggregates',
      'field_assessment_soil_structure',
      'field_assessment_plant_roots',
    ];
    // List of fields in consideration for calculating the presence of Soil
    // organic matter depletion.
    $soil_organic_keys = [
      'field_assessment_soil_cover',
      'field_assessment_residue_breakdown',
      'field_assessment_water_stable_aggregates',
      'field_assessment_soil_structure',
      'field_assessment_soil_color',
      'field_assessment_plant_roots',
      'field_assessment_biological_diversity',
      'field_assessment_biopores',
    ];
    // List of fields in consideration for calculating the presence of Soil
    // Organism Habitat Loss Or Degradation.
    $soil_organism_habitat_keys = [
      'field_assessment_soil_cover',
      'field_assessment_residue_breakdown',
      'field_assessment_surface_crusts',
      'field_assessment_water_stable_aggregates',
      'field_assessment_soil_structure',
      'field_assessment_plant_roots',
      'field_assessment_biological_diversity',
      'field_assessment_biopores',
    ];

    // List of Fields in consideration for calcuating the presence of Aggregate
    // Instability.
    $aggregate_instability_keys = [
      'field_assessment_soil_cover',
      'field_assessment_surface_crusts',
      'field_assessment_ponding',
      'field_assessment_water_stable_aggregates',
      'field_assessment_soil_structure',
      'field_assessment_plant_roots',
      'field_assessment_biological_diversity',
      'field_assessment_biopores',
    ];

    // Start: Compaction.
    $compaction_rc_present = NULL;
    $compaction_count = 0;

    foreach ($compaction_keys as $key) {
      if ($form_values[$key] == 0) {
        $compaction_count += 1;
      }
    }
    $compaction_rc_present = $compaction_count >= 2 || $form_values['field_assessment_soil_structure'] == 0;
    // End: Compaction.
    // Start: Soil Organic Matter Deplete Resource Concern calculation.
    $soil_organic_matter_rc_present = NULL;
    $soil_organic_matter_count = 0;

    // Tracks the number of fields with keys in "soil_organic_keys" that have
    // "Yes" as their response.
    foreach ($soil_organic_keys as $key) {
      if ($form_values[$key] == 0) {
        $soil_organic_matter_count += 1;
      }
    }
    $soil_organic_matter_rc_present = $soil_organic_matter_count >= 3;
    // End: Soil Organic Matter Deplete Resource Concern calculation.
    // Begin: Aggregate Instability Resource concern calculation.
    $aggregate_instability_rc_present = NULL;
    $aggregate_instability_count = 0;

    foreach ($aggregate_instability_keys as $key) {
      if ($form_values[$key] == 0) {
        $aggregate_instability_count += 1;
      }
    }
    $aggregate_instability_rc_present = $aggregate_instability_count >= 2 || $form_values['field_assessment_water_stable_aggregates'] == 0;
    // End: Aggregate Instability Resource concern calculation.
    // Begin: Soil Organism Habitat Resource Concern calculation.
    $soil_organism_habitat_rc_present = NULL;
    $soil_organism_habitat_count = 0;

    foreach ($soil_organism_habitat_keys as $key) {
      if ($form_values[$key] == 0) {
        $soil_organism_habitat_count += 1;
      }
    }
    $soil_organism_habitat_rc_present = $soil_organism_habitat_count >= 2;
    // End: Soil Organism Habitat Resource Concern calculation.
    // Start: Save calculated values into form state.
    $form_state->set('compaction_rc_present', $compaction_rc_present);
    $form_state->set('aggregate_instability_rc_present', $aggregate_instability_rc_present);
    $form_state->set('soil_organic_matter_rc_present', $soil_organic_matter_rc_present);
    $form_state->set('soil_organism_habitat_rc_present', $soil_organism_habitat_rc_present);

    $form_state->set('calculate_rcs', TRUE);
    // End: Save calculated values into form state.
    $form_state->setRebuild(TRUE);
  }

  /**
   * Ajax callback for calculating resource concerns.
   */
  public function calcuateResourceConcernsCallback(array &$form, FormStateInterface $form_state) {
    return $form['assessment_wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'field_assessment_form';
  }

}
