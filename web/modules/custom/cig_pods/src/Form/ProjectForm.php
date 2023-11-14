<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;
use Drupal\Core\Render\Element\Checkboxes;
use Drupal\cig_pods\ProjectAccessControlHandler;

/**
 * Project form.
 */
class ProjectForm extends PodsFormBase {

  /**
   * Get grant type options.
   * TODO Remove when determine how we handl acess control
   */
  public function getGrantTypeOptions() {
    $grand_type_options = [];
    $grand_type_options = [];
    $grand_type_options[''] = ' - Select -';

    $grand_type_options = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(
    ['vid' => 'd_grant_type']
    );
    $grand_type_keys = array_keys($grand_type_options);
    foreach ($grand_type_keys as $grand_type_key) {
      $term = $grand_type_options[$grand_type_key];
      $grand_type_options[$grand_type_key] = $term->getName();
    }

    return $grand_type_options;
  }

  /**
   * Get award options.
   */
  public function getAwardOptions() {
    $awards = [];
    $options = [];
    if(ProjectAccessControlHandler::isProjectManager()){
      $awards = ProjectAccessControlHandler::getProjectManagerAwardEntities();
      foreach($awards as $award){
        $award_entity = \Drupal::entityTypeManager()->getStorage('asset')->load($award);
        $options[$award] = $award_entity->get('field_award_agreement_number')->value;
      }
        
    }else{
      $awards = $this->entityOptions('asset', 'award');
      foreach($awards as $key => $award){
        $award_entity = \Drupal::entityTypeManager()->getStorage('asset')->load($key);
        $options[$key] = $award_entity->get('field_award_agreement_number')->value;
      }
    }

    

    return ['' => '- Select -'] + $options;
  }

  /**
   * Convert fraction to decimal.
   */
  private function convertFractionsToDecimal($is_edit, $project, $field) {
    if ($is_edit) {
      $num = $project->get($field)[0]->getValue()["numerator"];
      $denom = $project->get($field)[0]->getValue()["denominator"];
      return $num / $denom;
    }
    else {
      return "";
    }
  }

  /**
   * Get resource concern options.
   */
  public function getResourceConcernOptions() {
    $resource_concern_options = [];
    $resource_concern_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(
    ['vid' => 'd_resource_concern']
    );

    $resource_concern_keys = array_keys($resource_concern_terms);

    foreach ($resource_concern_keys as $resource_concern_key) {
      $term = $resource_concern_terms[$resource_concern_key];
      $resource_concern_options[$resource_concern_key] = $term->getName();
    }

    return $resource_concern_options;
  }

  /**
   * Get award agreement number options.
   */
  public function getAwardAgreementNumberOptions() {
    $awards = \Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['type' => 'award']);
    
    if (empty($awards)) {
      return ['' => ' - Select - '];
    }
    
    $agreement_number_options = ['' => ' - Select - '];

    foreach ($awards as $award_id => $award) {
      if ($award->hasField('field_award_agreement_number') && !$award->get('field_award_agreement_number')->isEmpty()) {
        $agreement_number = $award->get('field_award_agreement_number')->getValue()[0]['value'];
        $agreement_number_options[$award_id] = $agreement_number;
      }
    }
    
    return $agreement_number_options;
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL, AssetInterface $asset = NULL) {
    $project = $asset;
    $is_edit = $project <> NULL;

    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('project_id', $project->id());
    }
    else {
      $form_state->set('operation', 'create');
    }
    $form['#attached']['library'][] = 'cig_pods/project_entry_form';
    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'core/drupal.form';

    $form['form_title'] = [
      '#markup' => '<h1 id="form-title">Project</h1>',
    ];


    $form['subform_1'] = [
      '#markup' => '<div class="subform-title-container subform-title-container-top"><h2>Project Information</h2><h4>6 Fields | Section 1 of 2</h4></div>',
    ];

    $project_default_name = $is_edit ? $project->get('name')->value : '';
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Project Name'),
      '#required' => TRUE,
      '#default_value' => $project_default_name,
    ]; 

    $award_options = $this->getAwardOptions();

    $award_default = $is_edit ? $project->get('award')->target_id : '';
    $form['award'] = [
      '#type' => 'select',
      '#title' => $this->t('Award Agreement Number'),
      '#options' => $award_options,
      '#required' => TRUE,
      '#default_value' => $award_default,
    ];

    $grant_type_options = $this->getGrantTypeOptions();
    $grant_type_default = $is_edit ? $project->get('field_grant_type')->target_id : NULL;
    $form['field_grant_type'] = [
      '#type' => 'select',
      '#title' => 'Grant Type',
      '#options' => $grant_type_options,
      '#required' => TRUE,
      '#default_value' => $grant_type_default,
    ];

    $awardee_org_default_name = $this->convertFractionsToDecimal($is_edit, $project, 'field_funding_amount');
    $form['field_funding_amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Funding Amount'),
      '#required' => TRUE,
      '#default_value' => $awardee_org_default_name,
    ];

    $field_resource_concerns_default = $is_edit ? $project->get('field_resource_concerns')->getValue() : '';
    $resource_concern_options = $this->getResourceConcernOptions();
    $field_resource_concerns_default_final = [];
    foreach ($field_resource_concerns_default as $checks) {
      $field_resource_concerns_default_final = $checks['target_id'];
      $field_resource_concerns_defaultvalue[] = $field_resource_concerns_default_final;
    }

    $form['field_resource_concerns'] = [
      '#type' => 'select2',
      '#multiple' => TRUE,
      '#title' => $this->t('Possible Resource Concerns'),
      '#options' => $resource_concern_options,
      '#required' => TRUE,
      '#default_value' => $field_resource_concerns_defaultvalue,
    ];

    $summary_default = $is_edit ? $project->get('field_summary')->getValue()[0]['value'] : '';
    $form['field_summary'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Project Summary'),
      '#required' => TRUE,
      '#default_value' => $summary_default,
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
      '#submit' => ['::dashboardRedirect'],

    ];

    if ($is_edit) {
      $form['actions']['delete'] = [
        '#type' => 'submit',
        '#value' => $this->t('Delete'),
        '#submit' => ['::deleteProject'],
      ];
    }
    return $form;
  }

  /**
   * Delete project.
   */
  public function deleteProject(array &$form, FormStateInterface $form_state) {
    $project_id = $form_state->get('project_id');
    $project = \Drupal::entityTypeManager()->getStorage('asset')->load($project_id);

    try {
      $project->delete();
      $form_state->setRedirect('cig_pods.dashboard');
    }
    catch (\Exception $e) {
      $this
        ->messenger()
        ->addError($e->getMessage());
    }

  }

  /**
   * Returns True if all values in array is unique, false otherwise.
   */
  public function arrayValuesAreUnique($array) {
    $count_dict = array_count_values($array);

    foreach ($count_dict as $value) {
      if ($value != 1) {
        return FALSE;
      }
    }
    return TRUE;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    
  }

  /**
   * Redirect to PODS dashboard.
   */
  public function dashboardRedirect(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('cig_pods.dashboard');
  }

  /**
   * Get form entity mapping.
   */
  public function getFormEntityMapping() {
    $mapping = [];

    $mapping['name'] = 'name';
    $mapping['award'] = 'award';
    $mapping['field_funding_amount'] = 'field_funding_amount';
    $mapping['field_summary'] = 'field_summary';
    $mapping['field_grant_type'] = 'field_grant_type';

    return $mapping;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $is_create = $form_state->get('operation') === 'create';

    if ($is_create) {
      $mapping = $this->getFormEntityMapping();

      $project_submission = [];

      $project_submission['type'] = 'project';

      // Single value fields can be mapped in.
      foreach ($mapping as $form_elem_id => $entity_field_id) {
        // If mapping not in form or value is empty string.
        if ($form[$form_elem_id] === NULL || $form[$form_elem_id] === '') {
          continue;
        }
        $project_submission[$entity_field_id] = $form[$form_elem_id]['#value'];
      }
      // Read from multivalued checkbox.
      $checked_resource_concerns = Checkboxes::getCheckedCheckboxes($form_state->getValue('field_resource_concerns'));

      $project_submission['field_resource_concerns'] = $checked_resource_concerns;

      $project = Asset::create($project_submission);
      $project->save();


      $form_state->setRedirect('cig_pods.dashboard');
    }
    else {
      $project_id = $form_state->get('project_id');
      $project = \Drupal::entityTypeManager()->getStorage('asset')->load($project_id);

      $project_name = $form_state->getValue('name');
      $award_agreement_number = $form_state->getValue('award');
      $field_resource_concerns = $form_state->getValue('field_resource_concerns');
      $field_funding_amount = $form_state->getValue('field_funding_amount');
      $summary = $form_state->getValue('field_summary');
      $field_grant_type = $form_state->getValue('field_grant_type');

      $project->set('name', $project_name);
      $project->set('award', $award_agreement_number);
      $project->set('field_resource_concerns', $field_resource_concerns);
      $project->set('field_funding_amount', $field_funding_amount);
      $project->set('field_summary', $summary);
      $project->set('field_grant_type', $field_grant_type);
      $project->save();
      $form_state->setRedirect('cig_pods.dashboard');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'project_create_form';
  }

}
