<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;

/**
 * Producer form.
 */
class ProducerForm extends PodsFormBase {

  /**
   * Get asset options.
   */
  private function getAssetOptions($assetType) {
    $options = $this->entityOptions('asset', $assetType);
    return ['' => '- Select -'] + $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    $producer = $asset;

    $is_edit = $producer <> NULL;

    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('producer_id', $producer->id());
    }
    else {
      $form_state->set('operation', 'create');
    }

    $form['#attached']['library'][] = 'cig_pods/producer_form';
    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'core/drupal.form';

    $form['producer_title'] = [
      '#markup' => '<div><h1>Producer</h1></div>',
    ];

    $form['subform_1'] = [
      '#markup' => '<div class="subform-title-container subform-title-container-top"><h2>Producer Information</h2><h4>4 Fields | Section 1 of 1</h4></div>',
    ];
    
    $projects = $this->getAssetOptions('project');

    $producer_project_default_value = $is_edit ? $producer->get('project')->target_id : NULL;
    $form['field_producer_project'] = [
      '#type' => 'select',
      '#title' => $this->t('Producer project'),
      '#options' => $projects,
      '#required' => TRUE,
      '#default_value' => $producer_project_default_value,
    ];

    $producer_first_name_default_value = $is_edit ? $producer->get('field_producer_first_name')->value : '';
    $form['field_producer_first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Producer First Name'),
      '#required' => TRUE,
      '#default_value' => $producer_first_name_default_value,
    ];

    $producer_last_name_default_value = $is_edit ? $producer->get('field_producer_last_name')->value : '';
    $form['field_producer_last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Producer Last Name'),
      '#required' => TRUE,
      '#default_value' => $producer_last_name_default_value,
    ];

    $producer_headquarter_default_value = $is_edit ? $producer->get('field_producer_headquarter')->value : '';
    $form['field_producer_headquarter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Producer Headquarter Location'),
      '#required' => FALSE,
      '#default_value' => $producer_headquarter_default_value,
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
        '#submit' => ['::deleteProducer'],
      ];
    }

    return $form;
  }

  /**
   * Deletes the producer that is currently being viewed.
   */
  public function deleteProducer(array &$form, FormStateInterface $form_state) {
    $producer_id = $form_state->get('producer_id');
    $producer = \Drupal::entityTypeManager()->getStorage('asset')->load($producer_id);

    try {
      $producer->delete();
      $form_state->setRedirect('cig_pods.dashboard');
    }
    catch (\Exception $e) {
      $this
        ->messenger()
        ->addError($e->getMessage());
    }

  }

  /**
   * Redirect to PODS dashboard.
   */
  public function dashboardRedirect(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('cig_pods.dashboard');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $is_create = $form_state->get('operation') === 'create';

    // PHP: '1' == 1 is True but '1' === 1 is False.
    if ($is_create) {
      $producer_submission = [];
      $producer_submission['field_producer_first_name'] = $form_state->getValue('field_producer_first_name');
      $producer_submission['field_producer_last_name'] = $form_state->getValue('field_producer_last_name');
      $producer_submission['field_producer_headquarter'] = $form_state->getValue('field_producer_headquarter');
      $producer_submission['project'] = $form_state->getValue('field_producer_project');
      $producer_submission['type'] = 'producer';
      $producer_submission['name'] = $producer_submission['field_producer_first_name'] . " " . $producer_submission['field_producer_last_name'];

      $producer = Asset::create($producer_submission);
      $producer->save();

      $this->setAwardReference($producer, $form_state->getValue('field_producer_project'));

      $form_state->setRedirect('cig_pods.dashboard');
    }
    else {
      $id = $form_state->get('producer_id');
      $producer = \Drupal::entityTypeManager()->getStorage('asset')->load($id);

      $fn = $form_state->getValue('field_producer_first_name');
      $ln = $form_state->getValue('field_producer_last_name');
      $hq = $form_state->getValue('field_producer_headquarter');
      $pp = $form_state->getValue('field_producer_project');
      $full_n = $fn . " " . $ln;

      $producer->set('field_producer_first_name', $fn);
      $producer->set('field_producer_last_name', $ln);
      $producer->set('field_producer_headquarter', $hq);
      $producer->set('project', $pp);
      $producer->set('name', $full_n);

      $producer->save();

      $this->setAwardReference($producer, $form_state->getValue('field_producer_project'));

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

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'producer_create_form';
  }

}
