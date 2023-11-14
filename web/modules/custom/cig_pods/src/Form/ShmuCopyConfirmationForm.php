<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;
use Drupal\Core\Render\Element\Checkboxes;
use Drupal\geofield\GeoPHP\GeoPHPWrapper;

/**
 * SHMU form.
 */
class ShmuCopyConfirmationForm extends PodsFormBase {

  /**
   * Get land use options.
   */
  public function getLandUseOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_land_use');
    return ['' => '- Select -'] + $options;

  }

  /**
   * Get land use modifier options.
   */
  public function getLandUseModifierOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_land_use_modifiers');
    return $options;
  }

  /**
   * SHMU is a reference to SoilHealthManagmentUnit entity.
   */
  public function getDecimalFromShmuFractionFieldType(object $shmu, string $field_name) {
    return $shmu->get($field_name)->denominator == '' ? '' : $shmu->get($field_name)->numerator / $shmu->get($field_name)->denominator;
  }

  /**
   * Get default values array from multivalue SHMU field.
   *
   * Field_name must be a string relating to a field witn "multiple -> TRUE" in
   * its definition.
   */
  public function getDefaultValuesArrayFromMultivaluedShmuField(object $shmu, string $field_name) {
    $field_iter = $shmu->get($field_name);

    $populated_values = [];
    foreach ($field_iter as $term) {
      // This is the PHP syntax to append to the array.
      $populated_values[] = $term->target_id;
    }
    return $populated_values;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL, string $context = NULL) {
    $shmu = $asset;
    $is_edit = FALSE;

    if ($form_state->get('load_done') == NULL) {
      $form_state->set('load_done', FALSE);
    }

    $form_state->set('operation', 'edit');
    $form_state->set('shmu_id', $shmu->id());
    $form_state->set('original_crop_rotation_ids', $shmu_db_crop_rotations);

    // Attach the SHMU css library.
    $form['#attached']['library'][] = 'cig_pods/soil_health_management_unit_form';
    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'core/drupal.form';
    // Allows getting at the values hierarchy in form state.
    $form['#tree'] = TRUE;

    $form['title'] = [
      '#markup' => '<div class="title-container"><h1>Soil Health Management Unit (SHMU)</h1></div>',
    ];

    $form['copy_description'] = [
      '#markup' => '<div class="copy-description"><h2>Some previously filled sections have been copied to this SHMU and will not appear below.</h2></div>
      <div class="copy-ignored"><h4>SHMU Setup, Experimental Designs, Overview of the Productions System, 
      Crop Cover History, Tillage Type, Additional Concerns of Impacts and NRCS Practices.</h4></div>',
    ];


    $name_value = $shmu->get('name')->value;    
    // First section.
    $form['subform_1'] = [
      '#markup' => '<div class="subform-title-container section1"><h2>Copy of "'. $name_value .'" Set Up</h2><h4>1 Field | Section 1 of 4</h4></div>',
    ];


    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Soil Health Management Unit (SHMU) Name'),
      '#description' => '',
      '#default_value' => '',
      '#required' => TRUE,
    ];

    // New section (Geometry entry)
    $form['subform_3'] = [
      '#markup' => '<div class="subform-title-container section3"><h2>Soil Health Management Unit (SHMU) Map</h2><h4>1 Field | Section 2 of 4</h4> </div>',
    ];

    $form['static_1']['content'] = [
      '#markup' => '<div class="draw">Draw your Soil Health Management Unit (SHMU) on the map</div>',
    ];

    $form['mymap'] = [
      '#type' => 'farm_map_input',
      '#required' => TRUE,
      '#map_type' => 'pods',
      '#behaviors' => [
        'zoom_us',
        'wkt_refresh',
      ],
      '#map_settings' => [
        'behaviors' => [
          'nrcs_soil_survey' => [
            'visible' => TRUE,
          ],
        ],
      ],
      '#display_raw_geometry' => TRUE,
      '#default_value' => '',
    ];

    // New section (Soil and Treatment Identification)
    $form['subform_4'] = [
      '#markup' => '<div class="subform-title-container section4"><h2>Soil and Treatment Identification</h2><h4>2 Fields | Section 3 of 4</h4> </div>',
    ];
    $form['ssurgo_lookup'] = [
      '#type' => 'submit',
      '#value' => $this->t('Lookup via SSURGO'),
      '#ajax' => [
        'callback' => '::ssurgoDataCallback',
        'wrapper' => 'ssurgo-data',
      ],
      '#limit_validation_errors' => [['mymap']],
      '#submit' => ['::ssurgoDataLookup'],
    ];
    $form['ssurgo_symbol_text'] = [
            '#markup' => $this->t('<div class="ssurgo-symbol-text"><p>Click "Lookup via SSURGO" to place map symbols in the field below using the information from the map above.</p></div>')
        ];
    $form['ssurgo_data_wrapper'] = [
      '#type' => 'container',
      '#prefix' => '<div id="ssurgo-data">',
      '#suffix' => '</div>',
    ];
    $form['ssurgo_data_wrapper']['map_unit_symbol'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Map Unit Symbol'),
      '#default_value' => '',
    ];
     $form['ssurgo_data_wrapper']['ssurgo_texture_text'] = [
            '#markup' => $this->t('<div><p>Click "Lookup via SSURGO" to place soil textures in the field below using the information from the map above.</p></div>'),
        ];
    $form['ssurgo_data_wrapper']['surface_texture'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Map Unit Name'),
      '#default_value' => '',
    ];

   // New section (Land Use History)
   $form['subform_5'] = [
    '#markup' => '<div class="subform-title-container section5"><h2> Land Use History </h2><h4> 5 Fields | Section 4 of 4</h4></div>',
  ];
  $land_use_options = $this->getLandUseOptions();
  $form['field_shmu_prev_land_use'] = [
    '#type' => 'select',
    '#title' => $this->t('Previous Land Use'),
    '#options' => $land_use_options,
    '#default_value' => '',
    '#required' => FALSE,
  ];

  $land_use_modifier_options = $this->getLandUseModifierOptions();
  $form['field_shmu_prev_land_use_modifiers'] = [
    '#type' => 'checkboxes',
    '#title' => $this->t('Previous Land Use Modifiers'),
    '#options' => $land_use_modifier_options,
    '#default_value' => '',
    '#required' => FALSE,
  ];

  $form['field_shmu_date_land_use_changed'] = [
    '#type' => 'date',
    '#title' => $this->t('Date Land Use Changed'),
    '#description' => '',
  // Default value for "date" field type is a string in form of 'yyyy-MM-dd'.
    '#default_value' => '',
    '#required' => FALSE,
  ];

  $form['field_shmu_current_land_use'] = [
    '#type' => 'select',
    '#title' => $this->t('Current Land Use'),
    '#options' => $land_use_options,
    '#default_value' => '',
    '#required' => TRUE,
  ];

  $form['field_shmu_current_land_use_modifiers'] = [
    '#type' => 'checkboxes',
    '#title' => $this->t('Current Land Use Modifiers'),
    '#options' => $land_use_modifier_options,
    '#default_value' => '',
    '#required' => TRUE,
  ];
    
    $form['actions']['send'] = [
      '#type' => 'submit',
      '#value' => 'Save',
      
    ];
    
    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#limit_validation_errors' => '',
      '#submit' => ['::redirectAfterCancel'],
    ];

    return $form;

  }

  /**
   * Redirect after cancel.
   */
  public function redirectAfterCancel(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('cig_pods.dashboard');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'soil_health_management_unit_form';
  }

  public function addIrrigation(array &$form, FormStateInterface $form_state) {
    $form_state->set('irrigation_redirect', TRUE);
    $this->submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->messenger()->addStatus($this
    ->t('Copy Created, please adjust any fields if necessary<br>Your Previous SHMU has been copied successfully</br>'));

    $id = $form_state->get('shmu_id');
    $shmu_to_copy = Asset::load($id);

    //Array used for the creation of the new SHMU has values from the SHMU we are trying to copy and values gathered from the form
    $shmu_to_copy_fields = array(
      'field_shmu_involved_producer' => $shmu_to_copy->get('field_shmu_involved_producer')->target_id,
      'field_shmu_type' => $shmu_to_copy->get('field_shmu_type')->target_id,   
      'field_shmu_replicate_number' => $this->getDecimalFromShmuFractionFieldType($shmu_to_copy, 'field_shmu_replicate_number'),   
      'field_shmu_treatment_narrative' => $shmu_to_copy->get('field_shmu_treatment_narrative')->value,   
      'field_shmu_experimental_design' => $shmu_to_copy->get('field_shmu_experimental_design')->target_id,   
      'field_shmu_experimental_duration_month' => $this->getDecimalFromShmuFractionFieldType($shmu_to_copy, 'field_shmu_experimental_duration_month'),   
      'field_shmu_experimental_duration_year' => $this->getDecimalFromShmuFractionFieldType($shmu_to_copy, 'field_shmu_experimental_duration_year'),   
      'field_shmu_experimental_frequency_day' => $this->getDecimalFromShmuFractionFieldType($shmu_to_copy, 'field_shmu_experimental_frequency_day'),       
      'field_shmu_experimental_frequency_month' => $this->getDecimalFromShmuFractionFieldType($shmu_to_copy, 'field_shmu_experimental_frequency_month'),   
      'field_shmu_experimental_frequency_year' => $this->getDecimalFromShmuFractionFieldType($shmu_to_copy, 'field_shmu_experimental_frequency_year'), 
      'field_current_tillage_system' => $shmu_to_copy->get('field_current_tillage_system')->target_id,  
      'field_years_in_current_tillage_system' => $this->getDecimalFromShmuFractionFieldType($shmu_to_copy, 'field_years_in_current_tillage_system'),  
      'field_shmu_previous_tillage_system' => $shmu_to_copy->get('field_shmu_previous_tillage_system')->target_id,  
      'field_years_in_prev_tillage_system' => $this->getDecimalFromShmuFractionFieldType($shmu_to_copy, 'field_years_in_prev_tillage_system'),  
      'field_shmu_major_resource_concern' => $this->getDefaultValuesArrayFromMultivaluedShmuField($shmu_to_copy, 'field_shmu_major_resource_concern'),  
      'field_shmu_resource_concern' => $this->getDefaultValuesArrayFromMultivaluedShmuField($shmu_to_copy, 'field_shmu_resource_concern'),  
      'field_shmu_practices_addressed' => $this->getDefaultValuesArrayFromMultivaluedShmuField($shmu_to_copy, 'field_shmu_practices_addressed'),  
      'field_shmu_initial_crops_planted' => $this->getDefaultValuesArrayFromMultivaluedShmuField($shmu_to_copy, 'field_shmu_initial_crops_planted'),  
      'award' => $shmu_to_copy->get('award')->target_id,
      'field_shmu_prev_land_use' => $form_state->getValue('field_shmu_prev_land_use'),
      'field_shmu_prev_land_use_modifiers' => $form_state->getValue('field_shmu_prev_land_use_modifiers'),
      'field_shmu_date_land_use_changed' =>  $form_state->getValue('field_shmu_date_land_use_changed'), 
      'field_shmu_current_land_use_modifiers' =>  $form_state->getValue('field_shmu_current_land_use_modifiers'), 
      'field_shmu_current_land_use' =>  $form_state->getValue('field_shmu_current_land_use'),
      'name' =>  $form_state->getValue('name'),
    );

    // All of the fields that support multi-select checkboxes on the page.
    $checkboxes_fields = [
      'field_shmu_prev_land_use_modifiers',
      'field_shmu_current_land_use_modifiers',
    ];

    // All of the fields that support date input on the page.
    $date_fields = [
      'field_shmu_date_land_use_changed',
    ];

    $shmu = NULL;
    $shmu_template = [];
    $shmu_template['type'] = 'soil_health_management_unit';
    $shmu = Asset::create($shmu_template);

    foreach ($shmu_to_copy_fields as $key => $value){
      
      if (in_array($key, $checkboxes_fields)) {
        // Value is of type array (Multi-select). Use built-in Checkbox method.
        // Set directly on SHMU object.
        $shmu->set($key, Checkboxes::getCheckedCheckboxes($value));
        continue;
      }
      if (in_array($key, $date_fields)) {
        // $value is expected to be a string of format yyyy-mm-dd
        // Set directly on SHMU object.
        $shmu->set($key, strtotime($value));
        continue;
      }

      $shmu->set($key, $value);
    }

    //Copying the Crop Rotation Sequence to the new SHMU
    $crop_rotation_sequence_to_copy = $shmu_to_copy->get('field_shmu_crop_rotation_sequence')->getValue();
    $crop_rotation_sequence_array = [];
    
    foreach($crop_rotation_sequence_to_copy as $crop_rotation){
      $crop_rotation_sequence = Asset::load($crop_rotation['target_id']);
      $crop_rotation_copy = $crop_rotation_sequence->createDuplicate();
      $crop_rotation_copy->save();
      $crop_rotation_sequence_array[] = $crop_rotation_copy->id();
    }

    $shmu->set('field_shmu_crop_rotation_sequence', $crop_rotation_sequence_array);
    
    // Map submission logic.
    $shmu->set('field_geofield',  $form_state->getValue('mymap'));

    // Set map unit symbol and map unit name.
    $shmu->set('field_shmu_map_unit_symbol', $form_state->getValue('ssurgo_data_wrapper')['map_unit_symbol']);
    $shmu->set('field_shmu_surface_texture', $form_state->getValue('ssurgo_data_wrapper')['surface_texture']);


    $shmu->save();
    $form_state->setRedirect('cig_pods.edit_soil_health_management_unit_form', ['asset' => $shmu->get('id')->value]);
  }

  /**
   * Submit function for looking up soil data from SSURGO.
   */
  public function ssurgoDataLookup(array &$form, FormStateInterface $form_state) {

    // Get WKT from the map.
    $wkt = $form_state->getValue('mymap');

    // Validate the WKT.
    $valid_geometry = FALSE;
    if (!empty($wkt)) {
      $geophp = new GeoPHPWrapper();
      try {
        if ($geophp->load($wkt)) {
          $valid_geometry = TRUE;
        }
      }
      catch (\Exception $e) {
        $valid_geometry = FALSE;
      }
    }
    if (!$valid_geometry) {
      return;
    }

    // Query the NRCS Soil Data Access API for mapunit data.
    $mapunits = \Drupal::service('nrcs.soil_data_access')->mapunitWktQuery($wkt);

    // If map units were found...
    if (!empty($mapunits)) {

      // Extract the mapunit symbol(s) and name(s).
      $musyms = [];
      $munames = [];
      foreach ($mapunits as $mapunit) {
        $musyms[] = $mapunit['musym'];
        $munames[] = $mapunit['muname'];
      }

      // Assemble the symbol and texture inputs.
      $symbols = implode('; ', $musyms);
      $textures = implode('; ', $munames);

      // In order to replace textfield text, we must alter the raw user input
      // and trigger a form rebuild. It cannot be done simply with setValue().
      $input = $form_state->getUserInput();
      $input['ssurgo_data_wrapper']['map_unit_symbol'] = $symbols;
      $input['ssurgo_data_wrapper']['surface_texture'] = $textures;
      $form_state->setUserInput($input);
      $form_state->setRebuild(TRUE);
    }
  }

  /**
   * Ajax callback for the soil names field.
   */
  public function ssurgoDataCallback(array &$form, FormStateInterface $form_state) {
    return $form['ssurgo_data_wrapper'];
  }

}
