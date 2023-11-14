<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;

/**
 * Inputs form.
 */
class InputsForm extends PodsFormBase {

  /**
   * {@inheritdoc}
   */
  public function getInputOptions($parent = 0) {
    $options = [];

    // Load terms (with optional parent).
    $term_storage = \Drupal::service('entity_type.manager')->getStorage('taxonomy_term');
    $terms = $term_storage->loadByProperties([
      'vid' => 'd_input',
      'parent' => $parent,
    ]);

    // Populate options.
    foreach ($terms as $term) {
      $options[$term->id()] = $term->label();
    }

    return ['' => '- Select -'] + $options;
  }

  /**
   * Get cost type options.
   */
  public function getCostTypeOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_cost_type');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get operation ID.
   */
  public function getOperationReferenceId($input_id) {
    $operation_query = \Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(
            ['type' => 'operation', 'field_input' => $input_id]
        );
    $operation = reset($operation_query);
    return $operation->id();
  }

  /**
   * Get unit options.
   */
  public function getUnitOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_unit');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get cost sequence IDs.
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
   * Get other cost options.
   */
  public function getOtherCostsOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_cost_type');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Convert fraction to decimal.
   */
  private function convertFraction($fraction) {
    $num = $fraction->getValue()["numerator"];
    $denom = $fraction->getValue()["denominator"];
    return $num / $denom;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL, AssetInterface $operation = NULL) {
    $input = $asset;

    $is_edit = $asset <> NULL;

    if ($form_state->get('load_done') == NULL) {
      $form_state->set('load_done', FALSE);
    }
    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('input_id', $input->id());
      $form_state->set('operation_id', $this->getOperationReferenceId($input->id()));
      $input_cost_sequences_ids = $this->getCostSequenceIdsForInput($input);
      if (!$form_state->get('load_done')) {
        $this->loadOtherCostsIntoFormState($input_cost_sequences_ids, $form_state);
        $form_state->set('load_done', TRUE);
      }
    }
    else {
      if (!$form_state->get('load_done')) {
        $this->loadOtherCostsIntoFormState([], $form_state);
        $form_state->set('load_done', TRUE);
      }
      $form_state->set('operation', 'create');
      $form_state->set('operation_id', $operation->id());
    }

    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'cig_pods/inputs_form';
    $form['#attached']['library'][] = 'core/drupal.form';

    $current_operation = \Drupal::entityTypeManager()->getStorage('asset')->load($form_state->get('operation_id'));

    $form['title'] = [
      '#markup' => '<div class="title-container"><h1>Inputs</h1></div>',
    ];

    $form['subtitle_1'] = [
      '#markup' => '<div class="subtitle-container section1-subheading"><h2>Input Information</h2><h4>5 Fields | Section 1 of 3</h4></div>',
    ];

    $form['operation_description'] = [
      '#markup' => '<span class="operation-description"><h4>Operation (Determined from the operation you selected for this input)</h4></span>',
    ];

    $operation_taxonomy_name = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($current_operation->get('field_operation')->target_id);
    $form_state->set('current_operation_name', $operation_taxonomy_name->getName());
    $form['field_operation'] = [
      '#markup' => $this->t('<span class="operation"><h4>@operation_name</h4></span>', ['@operation_name' => $operation_taxonomy_name->getName()]),
    ];

    // For the Date input fields, we have to convert from UNIX to yyyy-mm-dd.
    if ($is_edit) {
      // $field_input_date_value is expected to be a UNIX timestamp
      $field_input_date_value = $input->get('field_input_date')->value;
      $default_value_input_date = date("Y-m-d", $field_input_date_value);
    }
    else {
      $default_value_input_date = '';
    }

    $form['field_input_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date'),
      '#description' => '',
    // Default value for "date" field type is a string in form of 'yyyy-MM-dd'.
      '#default_value' => $default_value_input_date,
      '#required' => TRUE,
    ];

    $field_input_category_value = $is_edit ? $input->get('field_input_category')->target_id : '';

    $form['field_input_category'] = [
      '#type' => 'select',
      '#title' => $this->t('Input Category'),
      '#options' => $this->getInputOptions(),
      '#default_value' => $field_input_category_value,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::inputCategoryCallback',
        'wrapper' => 'field_input',
      ],
    ];

    // Populate the options based on the selected category.
    $input_category = [];
    if ($form_state->getValue('field_input_category')) {
      $input_category = $form_state->getValue('field_input_category');
    }
    elseif ($field_input_category_value) {
      $input_category = $field_input_category_value;
    }

    $input_options = !empty($input_category) ? $this->getInputOptions($input_category) : [];

    $form['input_prefix'] = [
      '#type' => 'markup',
      '#markup' => '<span id="input-input">',
    ];

    $field_input_value = $is_edit ? $input->get('field_input')->target_id : '';

    $form['field_input'] = [
      '#type' => 'select',
      '#title' => $this->t('Input'),
      '#options' => $input_options,
      '#default_value' => $field_input_value,
      '#required' => FALSE,
      '#validated' => TRUE,
      '#prefix' => '<span id="field_input">',
      '#suffix' => '</span>',
    ];

    $field_unit_value = $is_edit ? $input->get('field_unit')->target_id : '';

    $form['field_unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Unit'),
      '#options' => $this->getUnitOptions(),
      '#default_value' => $field_unit_value,
      '#required' => TRUE,
    ];

    $form['input_suffix'] = [
      '#type' => 'markup',
      '#markup' => '</span>',
    ];

    $field_rate_units_value = $is_edit && $input->get('field_rate_units')[0] ? $this->convertFraction($input->get('field_rate_units')[0]) : '';

    $form['field_rate_units'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 1,
      '#min' => 0,
      '#title' => $this->t('Rate Units/Ac'),
      '#description' => '',
      '#default_value' => $field_rate_units_value,
      '#required' => TRUE,
    ];

    $form['subtitle_2'] = [
      '#markup' => '<div class="subtitle-container second-section-subheader-spacer"><h2>Custom Application</h2><h4>2 Fields | Section 2 of 3</h4></div>',
    ];

    $field_cost_per_unit_value = $is_edit && $input->get('field_cost_per_unit')[0] ? $this->convertFraction($input->get('field_cost_per_unit')[0]) : '';

    $form['field_cost_per_unit'] = [
      '#type' => 'number',
      '#min' => 0,
      '#step' => 0.01,
      '#min' => 0,
      '#title' => $this->t('Cost Per Unit'),
      '#description' => '',
      '#default_value' => $field_cost_per_unit_value,
      '#required' => FALSE,
      '#prefix' => '<div id="cost-per-unit"',
    ];

    $field_custom_application_unit_value = $is_edit ? $input->get('field_custom_application_unit')->target_id : '';

    $form['field_custom_application_unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Unit'),
      '#options' => $this->getUnitOptions(),
      '#default_value' => $field_custom_application_unit_value,
      '#required' => FALSE,
      '#suffix' => '</div>',
    ];

    $form['subtitle_3'] = [
      '#markup' => '<div id="other-costs-spacer " class="subtitle-container third-section-subheader-spacer"><h2>Other Costs</h2><h4>Section 3 of 3</h4></div>',
    ];

    $form['#tree'] = TRUE;

    $form['cost_sequence'] = [
      '#prefix' => '<div id ="cost_sequence">',
      '#suffix' => '</div>',
    ];

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
        '#min' => 0,
        '#default_value' => $cost_default_value,
        '#required' => $form_index > 0,
      ];
      $form['cost_sequence'][$fs_index]['field_cost_type'] = [
        '#type' => 'select',
        '#title' => 'Type',
        '#options' => $cost_options,
        '#default_value' => $cost_type_default_value,
        '#required' => $form_index > 0,
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

    $form['actions']['save-refresh'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Another Input'),
      '#submit' => ['::addAnotherInput'],
    ];

    if ($is_edit) {
      $form['actions']['delete'] = [
        '#type' => 'submit',
        '#value' => $this->t('Delete'),
        '#submit' => ['::deleteInputs'],
      ];
    }
    return $form;
  }

  /**
   * Add another input.
   */
  public function addAnotherInput(array &$form, FormStateInterface $form_state) {
    $form_state->set('input_redirect', TRUE);
    $this->submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inputform';
  }

  /**
   * Get form entity mapping.
   */
  public function getFormEntityMapping() {
    $mapping = [];
    $mapping['field_input_date'] = 'field_input_date';
    $mapping['field_input_category'] = 'field_input_category';
    $mapping['field_input'] = 'field_input';
    $mapping['field_unit'] = 'field_unit';
    $mapping['field_rate_units'] = 'field_rate_units';
    $mapping['field_cost_per_unit'] = 'field_cost_per_unit';
    $mapping['field_custom_application_unit'] = 'field_custom_application_unit';
    $mapping['field_cost'] = 'field_cost';
    $mapping['field_cost_type'] = 'field_cost_type';
    return $mapping;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $is_create = $form_state->get('operation') === 'create';
    $operation_reference = \Drupal::entityTypeManager()->getStorage('asset')->load($form_state->get('operation_id'));
    $form_values = $form_state->getValues();
    if ($is_create) {
      $mapping = $this->getFormEntityMapping();

      $input_submission = [];

      $input_submission['type'] = 'input';

      $operation_taxonomy_name = $form_state->get('current_operation_name');
      $input_taxonomy_name = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($form['field_input_category']['#value']);
      $input_submission['name'] = $operation_taxonomy_name . "_" . $input_taxonomy_name->getName() . "_" . $form['field_input_date']['#value'];

      // Single value fields can be mapped in.
      foreach ($mapping as $form_elem_id => $entity_field_id) {
        // If mapping not in form or value is empty string.
        if ($form[$form_elem_id] === NULL || $form[$form_elem_id] === '' || $form[$form_elem_id] === 'field_cost' ||$form[$form_elem_id] === 'field_cost_type') {
          continue;
        }
        $input_submission[$entity_field_id] = $form[$form_elem_id]['#value'];
      }

      $input_submission['field_input_date'] = strtotime($form['field_input_date']['#value']);

      $input_to_save = Asset::create($input_submission);

      $all_cost_sequences = $form_values['cost_sequence'];

      $cost_template = [];
      $cost_template['type'] = 'cost_sequence';

      foreach ($all_cost_sequences as $sequence) {
        if ($sequence['field_cost_type'] == NULL && $sequence['field_cost'] == NULL) {
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

      $input_to_save->set('field_input_cost_sequences', $cost_sequence_ids);

      $input_to_save->save();
      $this->setAwardReference($input_to_save, $operation_reference->id());

      $operation_reference->get('field_input')[] = $input_to_save->id();
      $operation_reference->save();
    }
    else {
      $operation_taxonomy_name = $form_state->get('current_operation_name');
      $input_taxonomy_name = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($form['field_input_category']['#value']);
      $input_id = $form_state->get('input_id');
      $input = \Drupal::entityTypeManager()->getStorage('asset')->load($input_id);
      $input->set('name', $operation_taxonomy_name . "_" . $input_taxonomy_name->getName() . "_" . $form['field_input_date']['#value']);
      $mapping = $this->getFormEntityMapping();
      // Single value fields can be mapped in.
      foreach ($mapping as $form_elem_id => $entity_field_id) {
        // If mapping not in form or value is empty string.
        if ($form[$form_elem_id] === NULL || $form[$form_elem_id] === '' || $form[$form_elem_id] === 'field_cost' ||$form[$form_elem_id] === 'field_cost_type') {
          continue;
        }
        $input->set($entity_field_id, $form[$form_elem_id]['#value']);
      }

      $all_cost_sequences = $form_values['cost_sequence'];

      $cost_template = [];
      $cost_template['type'] = 'cost_sequence';

      foreach ($all_cost_sequences as $sequence) {
        if ($sequence['field_cost_type'] == NULL && $sequence['field_cost'] == NULL) {
          continue;
        }
        // We always create a new cost sequence asset for each rotation.
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

      $input->set('field_input_cost_sequences', $cost_sequence_ids);
      $input->set('field_input_date', strtotime($form['field_input_date']['#value']));
      $input->save();
    }

    if ($form_state->get('input_redirect') == TRUE) {
      $form_state->setRedirect('cig_pods.inputs_form', ['operation' => $form_state->get('operation_id')]);
    }
    else {
      $form_state->setRedirect('cig_pods.dashboard');
    }
  }

  /**
   * Set award reference.
   */
  public function setAwardReference($assetReference, $operationReference) {
    $operation = \Drupal::entityTypeManager()->getStorage('asset')->load($operationReference);
    $award = \Drupal::entityTypeManager()->getStorage('asset')->load($operation->get('award')->target_id);
    $assetReference->set('award', $award);
    $assetReference->save();
  }

  /**
   * Redirect to PODS dashboard on cancel.
   */
  public function cancelSubmit(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('cig_pods.dashboard');
  }

  /**
   * Delete inputs.
   */
  public function deleteInputs(array &$form, FormStateInterface $form_state) {
    $input_id = $form_state->get('input_id');
    $input_to_delete = \Drupal::entityTypeManager()->getStorage('asset')->load($input_id);
    $sequence_ids = $this->getCostSequenceIdsForInput($input_to_delete);
    $operation_reference = \Drupal::entityTypeManager()->getStorage('asset')->load($form_state->get('operation_id'));
    $input_list = $operation_reference->get('field_input')->getValue();
    $updated_inputs = [];
    foreach ($input_list as $input) {
      if ($input['target_id'] != $input_id) {
        $updated_inputs[] = $input;
      }
    }

    try {
      $input_to_delete->delete();
      $operation_reference->set('field_input', $updated_inputs);
      $operation_reference->save();
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
   * Ajax callback for input category.
   */
  public function inputCategoryCallback(array &$form, FormStateInterface $form_state) {
    return $form['field_input'];
  }

  /**
   * Ajax callback for deleting cost sequence.
   */
  public function deleteCostSequenceCallback(array &$form, FormStateInterface $form_state) {
    return $form['cost_sequence'];
  }

}
