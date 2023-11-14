<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;

/**
 * Operation form.
 */
class OperationForm extends PodsFormBase {

  /**
   * Get SHMU options.
   */
  public function getShmuOptions() {
    $options = $this->entityOptions('asset', 'soil_health_management_unit');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get equipment options.
   */
  public function getEquipmentOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_tractor_self_propelled_machine');
    return ['' => '- Select -'] + $options;

  }

  /**
   * Get equipment ownership options.
   */
  public function getEquipmentOwnershipOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_equipment_ownership');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get operation options.
   */
  public function getOperationOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_operation_type');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get cost sequence IDs for operation.
   */
  public function getCostSequenceIdsForOperation($operation) {
    $cost_sequence_target_ids = [];

    // Expected type of FieldItemList.
    $field_shmu_cost_sequence_list = $operation->get('field_operation_cost_sequences');
    foreach ($field_shmu_cost_sequence_list as $value) {
      // $value is of type EntityReferenceItem (has access to value through
      // target_id)
      $cost_sequence_target_ids[] = $value->target_id;
    }
    return $cost_sequence_target_ids;
  }

  /**
   * Get cost sequence IDs for input.
   */
  public function getCostSequenceIdsForInput($input) {
    $cost_sequence_target_ids = [];

    // Expected type of FieldItemList.
    $field_cost_sequence_list = $input->get('field_input_cost_sequences');
    foreach ($field_cost_sequence_list as $value) {
      // $value is of type EntityReferenceItem (has access to value through
      // target_id)
      $cost_sequence_target_ids[] = $value->target_id;
    }
    return $cost_sequence_target_ids;
  }

  /**
   * Get asset.
   */
  public function getAsset($id) {
    // We use load instead of load by properties here because we are looking by
    // id.
    $asset = \Drupal::entityTypeManager()->getStorage('asset')->load($id);
    return $asset;

  }

  /**
   * Load other costs into $form_state.
   */
  public function loadOtherCostsIntoFormState($cost_sequence_ids, $form_state) {

    $sequences = [];

    $i = 0;
    foreach ($cost_sequence_ids as $cost_sequence_id) {
      $tmp_sequence = $this->getAsset($cost_sequence_id)->toArray();
      $sequences[$i] = [];
      $sequences[$i]['field_cost_type'] = $tmp_sequence['field_cost_type'];
      $sequences[$i]['field_cost'] = $tmp_sequence['field_cost'];
      $i++;
    }
    // If sequences is still empty, set a blank sequence at index 0.
    if ($i == 0) {
      $sequences[0]['field_cost_type'][0]['value'] = '';
      $sequences[0]['field_cost'][0]['target_id'] = '';
    }

    $form_state->set('sequences', $sequences);
  }

  /**
   * Get other costs options.
   */
  public function getOtherCostsOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_cost_type');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get decimal from SHMU fraction field.
   */
  public function getDecimalFromShmuFractionFieldType(object $shmu, string $field_name) {
    return $shmu->get($field_name)->denominator == '' ? '' : $shmu->get($field_name)->numerator / $shmu->get($field_name)->denominator;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    $operation = $asset;
    $is_edit = $operation <> NULL;
    $default_options[''] = '- Select -';

    if ($form_state->get('load_done') == NULL) {
      $form_state->set('load_done', FALSE);
    }

    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'cig_pods/operation_form';
    $form['#attached']['library'][] = 'core/drupal.form';
    $form['#tree'] = TRUE;
    // Determine if it is an edit process. If it is, load irrigation into local
    // variable.
    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('operation_id', $operation->id());
      $operation_cost_sequences_ids = $this->getCostSequenceIdsForOperation($operation);
      if (!$form_state->get('load_done')) {
        $this->loadOtherCostsIntoFormState($operation_cost_sequences_ids, $form_state);
        $form_state->set('load_done', TRUE);
      }
      $form_state->set('original_cost_sequence_ids', $operation_cost_sequences_ids);
    }
    else {
      if (!$form_state->get('load_done')) {
        $this->loadOtherCostsIntoFormState([], $form_state);
        $form_state->set('load_done', TRUE);
      }
      $form_state->set('operation', 'create');
    }
    $form['subform_1'] = [
      '#markup' => '<div class="subform-title-container title-spacer"><h1>Operation</h1></div>',
    ];

    $form['subform_2'] = [
      '#markup' => '<div class="subform-title-container subform-title-container-top"><h2>Operation Information</h2><h4>4 Fields | Section 1 of 3</h4></div>',
    ];

    $shmu_options = $this->getShmuOptions();
    $shmu_default_value = $is_edit ? $operation->get('shmu')->target_id : $default_options;
    $form['shmu'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a Soil Health Management Unit (SHMU)'),
      '#options' => $shmu_options,
      '#default_value' => $shmu_default_value,
      '#required' => TRUE,
    ];

    if ($is_edit) {
      // $ field_operation_timestamp is expected to be a UNIX timestamp
      $field_operation_timestamp = $operation->get('field_operation_date')->value;
      $field_operation_timestamp_default_value = date("Y-m-d", $field_operation_timestamp);
    }
    else {
      $field_operation_timestamp_default_value = '';
    }

    $form['field_operation_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Operation Date'),
      '#description' => '',
      '#default_value' => $field_operation_timestamp_default_value,
      '#required' => TRUE,
    ];

    $field_operation_type = $is_edit ? $operation->get('field_operation')->target_id : '';
    $field_operation_options = $this->getOperationOptions();
    $form['field_operation'] = [
      '#type' => 'select',
      '#title' => $this->t('Operation'),
      '#default_value' => $field_operation_type,
      '#options' => $field_operation_options,
      '#required' => TRUE,
    ];

    $field_ownership_implement = $is_edit ? $operation->get('field_ownership_status')->target_id : '';
    $field_ownership_options = $this->getEquipmentOwnershipOptions();
    $form['field_ownership_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Equipment/Implement Ownership Status'),
      '#options' => $field_ownership_options,
      '#default_value' => $field_ownership_implement,
      '#required' => TRUE,
    ];

    $form['subform_3'] = [
      '#markup' => '<div class="subform-title-container subheader-spacer"><h2>Tractor/Self-Propelled Machine Information</h2><h4>4 Fields | Section 2 of 3</h4></div>',
    ];

    $field_tractor_self = $is_edit ? $operation->get('field_tractor_self_propelled_machine')->target_id : '';
    $field_equipment_options = $this->getEquipmentOptions();
    $form['field_tractor_self_propelled_machine'] = [
      '#type' => 'select',
      '#title' => $this->t('Tractor/Self-Propelled Machine'),
      '#options' => $field_equipment_options,
      '#default_value' => $field_tractor_self,
      '#required' => TRUE,
    ];

    $field_number_of_rows = $is_edit ? $this->getDecimalFromShmuFractionFieldType($operation, 'field_row_number') : '';

    $form['field_row_number'] = [
      '#type' => 'number',
      // Capped at 1 million because you can't have more than 1 million parts
      // per million.
      '#min' => 0,
      // Float.
      '#step' => 1,
      '#title' => $this->t('Number of Rows'),
      '#default_value' => $field_number_of_rows,
      '#required' => FALSE,
    ];

    $field_width_of = $is_edit ? $this->getDecimalFromShmuFractionFieldType($operation, 'field_width') : '';

    $form['field_width'] = [
      '#type' => 'number',
      '#min' => 0,
      // Capped at 1 million because you can't have more than 1 million parts
      // per million.
      '#max' => 1000000,
      // Float.
      '#step' => 0.01,
      '#title' => $this->t('Width'),
      '#default_value' => $field_width_of,
      '#required' => FALSE,
    ];

    $field_horsepower_of = $is_edit ? $this->getDecimalFromShmuFractionFieldType($operation, 'field_horsepower') : '';

    $form['field_horsepower'] = [
      '#type' => 'number',
      '#min' => 0,
      // Capped at 1 million because you can't have more than 1 million parts
      // per million.
      '#max' => 1000000,
      // Float.
      '#step' => 0.01,
      '#title' => $this->t('Horsepower'),
      '#default_value' => $field_horsepower_of,
      '#required' => FALSE,
    ];

    $form['subform_4'] = [
      '#markup' => '<div class="subform-title-container subheader-spacer"><h2>Other Costs</h2><h4>2 Fields | Section 3 of 3</h4></div>',
    ];

    $form['cost_sequence'] = [
      '#prefix' => '<div id ="cost_sequence">',
      '#suffix' => '</div>',
    ];
    // Get Options.
    $cost_options = $this->getOtherCostsOptions();

    $fs_cost_sequences = $form_state->get('sequences');

    // Not to be confused with rotation.
    $form_index = 0;
    foreach ($fs_cost_sequences as $fs_index => $sequence) {

      if ($sequence['field_cost'][0]['denominator'] == '') {
        $cost_default_value = '';
      } else {
        $cost_default_value = $sequence['field_cost'][0]['numerator'] / $sequence['field_cost'][0]['denominator'];
      }

      $cost_type_default_value = $sequence['field_cost_type'][0]['target_id'];

      $form['cost_sequence'][$fs_index] = [
        '#prefix' => '<div id="cost_rotation">',
        '#suffix' => '</div>',
      ];

      $form['cost_sequence'][$fs_index]['field_cost'] = [
        '#type' => 'number',
        '#min' => 0,
        '#title' => 'Cost',
        '#step' => 0.01,
        '#default_value' => $cost_default_value,
      ];
      $form['cost_sequence'][$fs_index]['field_cost_type'] = [
        '#type' => 'select',
        '#title' => 'Type',
        '#options' => $cost_options,
        '#default_value' => $cost_type_default_value,
      ];

      $form['cost_sequence'][$fs_index]['actions']['delete'] = [
        '#type' => 'submit',
        '#name' => 'delete-cost-' . $fs_index,
        '#submit' => ['::deleteCostSequence'],
        '#ajax' => [
          'callback' => "::deleteCostSequenceCallback",
          'wrapper' => 'cost_sequence',
        ],
        '#limit_validation_errors' => [],
        '#value' => $this->t('Delete'),
      ];

      // Very important.
      $form_index = $form_index + 1;
      // End very important.
    }

    $form['addCost'] = [
      '#type' => 'submit',
      '#submit' => ['::addAnotherCostSequence'],
      '#ajax' => [
        'callback' => '::addAnotherCostSequenceCallback',
        'wrapper' => 'cost_sequence',
      ],
      '#limit_validation_errors' => [],
      '#value' => 'Add Another Cost',
    ];


	$asset_id = $is_edit ? $asset->id() : NULL;

	$form['asset_id'] = [
	  '#type' => 'hidden',
	  '#value' => $asset_id,
	  '#attributes' => ['id' => ['asset_id'],],
	];
	
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),

    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#submit' => [[$this, 'cancelSubmit']],
      '#limit_validation_errors' => [],
    ];
    $form['actions']['add_input'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Inputs'),
      '#submit' => ['::addInput'],
    ];

    if ($is_edit) {
      $form['actions']['delete'] = [
        '#type' => 'submit',
        '#value' => $this->t('Delete'),
        '#submit' => [[$this, 'deleteSubmit']],
        '#limit_validation_errors' => [],
      ];
    }

    return $form;
  }

  /**
   * Cancel submit.
   */
  public function cancelSubmit(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('cig_pods.dashboard');
  }

  /**
   * Delete submit.
   */
  public function deleteSubmit(array &$form, FormStateInterface $form_state) {
    $id = $form_state->get('operation_id');
    $operation = \Drupal::entityTypeManager()->getStorage('asset')->load($id);
    $sequence_ids = $this->getCostSequenceIdsForOperation($operation);
    $inputs = $operation->get('field_input')->referencedEntities();
    try {
      $operation->delete();
      foreach ($inputs as $input) {
        $this->deleteInputs($input);
      }
      $form_state->setRedirect('cig_pods.dashboard');
    }
    catch (\Exception $e) {
      $this
        ->messenger()
        ->addError($e->getMessage());
    }
    foreach ($sequence_ids as $sid) {
      try {
        $cost_sequence = \Drupal::entityTypeManager()->getStorage('asset')->load($sid);
        $cost_sequence->delete();
      }
      catch (\Exception $e) {
        $this
          ->messenger()
          ->addError($e->getMessage());
      }
    }
  }

  /**
   * Delete inputs.
   */
  public function deleteInputs($input_to_delete) {
    $sequence_ids = $this->getCostSequenceIdsForInput($input_to_delete);
    try {
      $input_to_delete->delete();
    }
    catch (\Exception $e) {
      $this
        ->messenger()
        ->addError($e->getMessage());
    }
    foreach ($sequence_ids as $sid) {
      try {
        $cost_sequence = \Drupal::entityTypeManager()->getStorage('asset')->load($sid);
        $cost_sequence->delete();
      }
      catch (\Exception $e) {
        $this
          ->messenger()
          ->addError($e->getMessage());
      }
    }
  }

  /**
   * Add input.
   */
  public function addInput(array &$form, FormStateInterface $form_state) {
    $form_state->set('input_redirect', TRUE);
    $this->submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {

    return 'operation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $cost_fields = [
      'sequences',
      'cost_sequence',
      'field_cost',
      'field_cost_type',
    ];
    $is_edit = $form_state->get('operation') == 'edit';
    $ignored_fields = [
      'send',
      'form_build_id',
      'form_token',
      'form_id',
      'op',
      'actions',
      'delete',
      'cancel',
      'add_input',
      'addCost',
      'asset_id',
    ];
    $date_fields = ['field_operation_date'];
    $form_values = $form_state->getValues();

    if (!$is_edit) {
      $operation_template = [];
      $operation_template['type'] = 'operation';
      $operation = Asset::create($operation_template);
    }
    else {
      // Operation is of type Edit.
      $id = $form_state->get('operation_id');
      $operation = \Drupal::entityTypeManager()->getStorage('asset')->load($id);
    }

    // Set the operation asset name to "Operation {{asset-id}}".
    $operation->set('name', 'Operation ' . $id);

    foreach ($form_values as $key => $value) {
      // If it is an ignored field, skip the loop.
      if (in_array($key, $ignored_fields)) {
        continue;
      }
      if (in_array($key, $date_fields)) {
        // $value is expected to be a string of format yyyy-mm-dd
        // Set directly on SHMU object.
        $operation->set($key, strtotime($value));
        continue;
      }
      if (in_array($key, $cost_fields)) {
        continue;
      }

      $operation->set($key, $value);
    }

    $all_cost_sequences = $form_values['cost_sequence'];

    $cost_template = [];
    $cost_template['type'] = 'cost_sequence';

    foreach ($all_cost_sequences as $sequence) {
      // If they did not select a cost for the row, do not include it in the
      // save.
      if ($sequence['field_cost_type'] == NULL) {
        continue;
      }
      // We alwasys create a new cost sequence asset for each rotation.
      $cost_sequence = Asset::create($cost_template);

      // Read the cost id from select dropdown for given rotation.
      $cost_id = $sequence['field_cost_type'];
      $cost_sequence->set('field_cost_type', $cost_id);

      // Read the cost rotation year from select dropdown for given rotation.
      $cost_value = $sequence['field_cost'];
      $cost_sequence->set('field_cost', $cost_value);

      $cost_sequence->save();

      $cost_sequence_ids[] = $cost_sequence->id();
    }

    $operation->set('field_operation_cost_sequences', $cost_sequence_ids);
    $operation->save();

    $this->setAwardReference($operation, $operation->get('shmu')->target_id);

    // Cleanup - remove the old cost Sequence Assets that are no longer used.
    if ($is_edit) {
      $trash_rotation_ids = $form_state->get('original_cost_sequence_ids');
      foreach ($trash_rotation_ids as $key => $id) {
        $cost_sequence_old = Asset::load($id);
        $cost_sequence_old->delete();
      }
    }
    // Success message done.
    if ($form_state->get('input_redirect')) {
      $form_state->setRedirect('cig_pods.inputs_form', ['operation' => $operation->get('id')->value]);
    }
    else {
      $form_state->setRedirect('cig_pods.dashboard');
    }
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
   * Add another cost sequence.
   */
  public function addAnotherCostSequence(array &$form, FormStateInterface $form_state) {
    $sequences = $form_state->get('sequences');

    $new_cost_sequence = [];
    $new_cost_sequence['field_cost'][0]['value'] = '';
    $new_cost_sequence['field_cost_type'][0]['target_id'] = '';

    $sequences[] = $new_cost_sequence;
    $form_state->set('sequences', $sequences);

    $form_state->setRebuild(TRUE);
  }

  /**
   * Ajax callback for adding another cost sequence.
   */
  public function addAnotherCostSequenceCallback(array &$form, FormStateInterface $form_state) {

    return $form['cost_sequence'];
  }

  /**
   * Delete cost sequence.
   */
  public function deleteCostSequence(array &$form, FormStateInterface $form_state) {
    $idx_to_rm = str_replace('delete-cost-', '', $form_state->getTriggeringElement()['#name']);

    $sequences = $form_state->get('sequences');

    // Remove the index.
    unset($sequences[$idx_to_rm]);

    $form_state->set('sequences', $sequences);

    $form_state->setRebuild(TRUE);
  }

  /**
   * Ajax callback for deleting cost sequence.
   */
  public function deleteCostSequenceCallback(array &$form, FormStateInterface $form_state) {
    return $form['cost_sequence'];
  }

}
