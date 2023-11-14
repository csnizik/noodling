<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;

/**
 * Awardee organization form.
 */
class AwardeeOrganizationInfoForm extends PodsFormBase {

  /**
   * Get options for state territory terms.
   */
  private function getStateTerritoryOptions() {
    $options = $this->entityOptions('taxonomy_term', 'd_state_territory');
    return ['' => '- Select -'] + $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    $awardee = $asset;
    $organization_state_territory = $this->getStateTerritoryOptions();
    $is_edit = $awardee <> NULL;

    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('awardee_id', $awardee->id());
    }
    else {
      $form_state->set('operation', 'create');
    }

    // Attach proper CSS to form.
    $form['#attached']['library'][] = 'cig_pods/awardee_organization_form';
    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'core/drupal.form';

    $form['form_title'] = [
      '#markup' => '<h1>Awardee Organization</h1>',
    ];

    $form['subform_1'] = [
      '#markup' => '<div class="subform-title-container subform-title-container-top"><h2>Organization Information</h2><h4>4 Fields | Section 1 of 1</h4></div>',
    ];

    $awardee_org_default_name = $is_edit ? $awardee->get('name')->value : '';
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Awardee Organization Name'),
      '#required' => TRUE,
      '#default_value' => $awardee_org_default_name,
    ];

    $awardee_org_deault_short_name = $is_edit ? $awardee->get('organization_short_name')->value : '';
    $form['organization_short_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Awardee Organization Short Name'),
      '#required' => TRUE,
      '#default_value' => $awardee_org_deault_short_name,
    ];

    $awardee_org_deault_acronym = $is_edit ? $awardee->get('organization_acronym')->value : '';
    $form['organization_acronym'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Awardee Organization Acronym'),
      '#required' => TRUE,
      '#default_value' => $awardee_org_deault_acronym,
    ];

    $awardee_org_default_state_territory = $is_edit ? $awardee->get('organization_state_territory')->target_id : NULL;
    $form['organization_state_territory'] = [
      '#type' => 'select',
      '#title' => 'Awardee Organization State Or Territory',
      '#options' => $organization_state_territory,
      '#required' => TRUE,
      '#default_value' => $awardee_org_default_state_territory,
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
        '#submit' => ['::deleteAwardee'],
      ];
    }

    return $form;
  }

  /**
   * Redirect to the PODS dashboard.
   */
  public function dashboardRedirect(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('cig_pods.dashboard');
  }

  /**
   * Delete awardee.
   */
  public function deleteAwardee(array &$form, FormStateInterface $form_state) {
    $awardee_id = $form_state->get('awardee_id');
    $awardee = \Drupal::entityTypeManager()->getStorage('asset')->load($awardee_id);

    try {
      $awardee->delete();
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
    $is_create = $form_state->get('operation') === 'create';

    if ($is_create) {
      $awardee_submission = [];
      $awardee_submission['name'] = $form_state->getValue('name');
      $awardee_submission['organization_acronym'] = $form_state->getValue('organization_acronym');
      $awardee_submission['organization_short_name'] = $form_state->getValue('organization_short_name');
      $awardee_submission['organization_state_territory'] = $form_state->getValue('organization_state_territory');
      $awardee_submission['type'] = 'awardee';

      $awardee = Asset::create($awardee_submission);
      $awardee->save();

      $form_state->setRedirect('cig_pods.dashboard');
    }
    else {
      $awardee_id = $form_state->get('awardee_id');
      $awardee = \Drupal::entityTypeManager()->getStorage('asset')->load($awardee_id);

      $awardee_name = $form_state->getValue('name');
      $awardee_short_name = $form_state->getValue('organization_short_name');
      $awardee_state_territory = $form_state->getValue('organization_state_territory');
      $awardee_acronym = $form_state->getValue('organization_acronym');

      $awardee->set('name', $awardee_name);
      $awardee->set('organization_short_name', $awardee_short_name);
      $awardee->set('organization_state_territory', $awardee_state_territory);
      $awardee->set('organization_acronym', $awardee_acronym);

      $awardee->save();
      $form_state->setRedirect('cig_pods.dashboard');

    }

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'awardee_org_create_form';
  }

}
