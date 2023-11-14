<?php

namespace Drupal\cig_pods\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset\Entity\Asset;
use Drupal\cig_pods\ProjectAccessControlHandler;

/**
 * Awardee organization form.
 */
class AwardForm extends PodsFormBase {

  /**
   * Get awardee options.
   */
  public function getAwardeeOptions() {
    $options = $this->entityOptions('asset', 'awardee');
    return ['' => '- Select -'] + $options;
  }

  /**
   * Get referenced contacts.
   */
  public function getReferencedContacts(array $form, FormStateInterface $form_state, $award_id) {
    $contacts = \Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(
    ['type' => 'contact', 'award' => $award_id]
     );
    $form_state->set('contacts', $contacts);
  }


  /**
   * Add contacts to array.
   */
  public function addContactsToArray(string $zRoleType, array &$contact_name_options, array &$contact_options_email) {
    $zRoleContacts = \Drupal::service('usda_eauth.zroles')->getListByzRole($zRoleType);

    foreach ($zRoleContacts as $zContacts) {
      if (gettype($zContacts->UsdaeAuthenticationId) != "string") {
        continue; // discard users with no eauth id
      }
      
      if (array_key_exists($zContacts->UsdaeAuthenticationId, $contact_name_options)) {
        continue;
      }
      
      $contact_name_options[$zContacts->UsdaeAuthenticationId] = $zContacts->FirstName . ' ' . $zContacts->LastName;
      $contact_options_email[$zContacts->UsdaeAuthenticationId] = $zContacts->EmailAddress;
    }
  }
  /**
   * Get awardee contact type options.
   */
  public function getAwardeeContactTypeOptions() {
    $contact_type_options = [];
    $contact_type_options[''] = ' - Select -';

    $contact_type_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(
           ['vid' => 'd_contact_type']
     );
    $contact_type_keys = array_keys($contact_type_terms);
    foreach ($contact_type_keys as $contact_type_key) {
      $term = $contact_type_terms[$contact_type_key];
      $contact_type_options[$contact_type_key] = $term->getName();
    }

    return $contact_type_options;
  }

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
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AssetInterface $asset = NULL) {
    $award = $asset;
    $is_edit = $award <> NULL;
    $is_admin = ProjectAccessControlHandler::isAdmin();
    
    if ($is_edit) {
      $form_state->set('operation', 'edit');
      $form_state->set('award_id', $award->id());
      $this->getReferencedContacts($form, $form_state, $award->id());
    }
    else {
      $form_state->set('operation', 'create');
    }

    // Attach proper CSS to form.
    $form['#attached']['library'][] = 'cig_pods/project_entry_form';
    $form['#attached']['library'][] = 'cig_pods/css_form';
    $form['#attached']['library'][] = 'core/drupal.form';

    // Get num of contacts showing on screen. (1->n exclude:removed indexes)
    $num_contact_lines = $form_state->get('num_contact_lines');
    // Get num of added contacts. (1->n)
    $num_contacts = $form_state->get('num_contacts');
    // Get removed contacts indexes.
    $removed_contacts = $form_state->get('removed_contacts');
    $contact_default = $is_edit ? $form_state->get('contacts') : '';
    $contacts = [];
    foreach ($contact_default as $contact) {
      $contacts[] = $contact;
      
    }
    
    $ex_count = count($contacts);

    if ($is_edit) {
      // Initialize number of contact, set to 1.
      if ($num_contacts === NULL) {
        $form_state->set('num_contacts', $ex_count);
        $num_contacts = $form_state->get('num_contacts');
      }
      if ($num_contact_lines === NULL) {
        $form_state->set('num_contact_lines', $ex_count);
        $num_contact_lines = $form_state->get('num_contact_lines');
      }

    }
    else {
      // Initialize number of contact, set to 1.
      if ($num_contacts === NULL) {
        $form_state->set('num_contacts', 1);
        $num_contacts = $form_state->get('num_contacts');
      }
      if ($num_contact_lines === NULL) {
        $form_state->set('num_contact_lines', 1);
        $num_contact_lines = $form_state->get('num_contact_lines');
      }
    }

    if ($removed_contacts === NULL) {
      // Initialize arr.
      $form_state->set('removed_contacts', []);
      $removed_contacts = $form_state->get('removed_contacts');
    }

    $form['form_title'] = [
      '#markup' => '<h1>Award</h1>',
    ];

    $form['subform_1'] = [
      '#markup' => '<div class="subform-title-container subform-title-container-top"><h2>Award Identification and Access Control</h2><h4>3 Fields | Section 1 of 1</h4></div>',
    ];

    $awardee_options = $this->getAwardeeOptions();

    $awardee_default_name = $is_edit ? $award->get('field_award_awardee_org')->target_id : NULL;
    $form['field_award_awardee_org'] = [
      '#type' => 'select',
      '#title' => 'Awardee Organization Name',
      '#options' => $awardee_options,
      '#required' => TRUE,
      '#default_value' => $awardee_default_name,
      '#access' => $is_admin,
    ];

    $agreement_number_default = $is_edit ? $award->get('field_award_agreement_number')->getValue()[0]['value'] : '';
    $form['field_award_agreement_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Agreement Number'),
      '#default_value' => $agreement_number_default,
      '#required' => TRUE,
      '#disabled' => !$is_admin,
    ];
    
    $contact_type_options = $this->getAwardeeContactTypeOptions();
    $form['#tree'] = TRUE;
    $form['names_fieldset'] = [
      '#prefix' => '<div id="names-fieldset-wrapper"',
      '#suffix' => '</div>',
    ];

    // num_contacts: get num of added contacts. (1->n)
    for ($i = 0; $i < $num_contacts; $i++) {

      // Check if field was removed.
      if (in_array($i, $removed_contacts)) {
        continue;
      }

      $default_name = $is_edit && !empty($contacts[$i]) ? $contacts[$i]->get('name')->value : NULL;
      $default_type = $is_edit && !empty($contacts[$i]) ? $contacts[$i]->get('field_contact_type')->target_id : NULL;
      $default_email = $is_edit && !empty($contacts[$i]) ? $contacts[$i]->get('field_contact_email')->value : NULL;
      $default_euath_id = $is_edit && !empty($contacts[$i]) ? $contacts[$i]->get('eauth_id')->value : NULL;

      $form['names_fieldset'][$i]['contact_name'] = [
        '#type' => 'textfield',
        '#title' => $this
          ->t("Contact Name"),
        '#required' => TRUE,
        '#default_value' => $default_name,
        'attributes' => [
          'class' => 'something',
        ],
        '#prefix' => ($num_contact_lines > 1) ? '<div class="inline-components-short">' : '<div class="inline-components">',
        '#suffix' => '</div>',
      ];

      $form['names_fieldset'][$i]['contact_type'] = [
        '#type' => 'select',
        '#title' => $this
          ->t('Contact Type'),
        '#options' => $contact_type_options,
        '#default_value' => $default_type,
        '#prefix' => '<div class="inline-components"',
        '#suffix' => '</div>',
        '#required' => TRUE,
      ];

      $form['names_fieldset'][$i]['contact_eauth_id'] = [
        '#type' => 'textfield',
        '#title' => $this
          ->t("Contact's Eauth ID"),
        '#required' => TRUE,
        '#default_value' => $default_euath_id,
        'attributes' => [
          'class' => 'something',
        ],
        '#prefix' => ($num_contact_lines > 1) ? '<div class="inline-components-short">' : '<div class="inline-components">',
        '#suffix' => '</div>',
      ];

      $form['names_fieldset'][$i]['contact_email'] = [
        '#type' => 'textfield',
        '#title' => $this
          ->t("Contact's Email"),
        '#required' => TRUE,
        '#default_value' => $default_email,
        'attributes' => [
          'class' => 'something',
        ],
        '#prefix' => ($num_contact_lines > 1) ? '<div class="inline-components-short">' : '<div class="inline-components">',
        '#suffix' => '</div>',
      ];

      if ($num_contact_lines > 1 && $i != 0) {
        $form['names_fieldset'][$i]['actions'] = [
          '#type' => 'submit',
          '#value' => $this->t('Delete'),
          '#name' => 'delete-contact-' . $i,
          '#submit' => ['::removeContact'],
          '#ajax' => [
            'callback' => '::contactRowCallback',
            'wrapper' => 'names-fieldset-wrapper',
          ],
          "#limit_validation_errors" => [],
          '#prefix' => '<div class="remove-button-container">',
          '#suffix' => '</div>',
        ];
      }

      // Css space for a new line due to previous items' float left attr.
      $form['names_fieldset'][$i]['new_line_container'] = [
        '#markup' => '<div class="clear-space"></div>',
      ];
    }
  
    
    $form['actions'] = [
      '#type' => 'actions',
    ];

  $form['names_fieldset']['actions']['add_name'] = [
    '#type' => 'submit',
    '#button_type' => 'button',
    '#name' => 'add_contact_button',
    '#value' => $this->t('Add Another Contact'),
    '#submit' => ['::addContactRow'],
    '#ajax' => [
      'callback' => '::contactRowCallback',
      'wrapper' => 'names-fieldset-wrapper',
    ],
    '#states' => [
      'visible' => [
        [":input[name='names_fieldset[0][contact_name]']" => ['!value' => '']],
        "and",
        [":input[name='names_fieldset[0][contact_type]']" => ['!value' => '']],
      ],
    ],
    "#limit_validation_errors" => [],
    '#prefix' => '<div id="addmore-button-container">',
    '#suffix' => '</div>',
  ];


	$asset_id = $is_edit ? $asset->id() : NULL;

	$form['asset_id'] = [
	  '#type' => 'hidden',
	  '#value' => $asset_id,
	  '#attributes' => ['id' => ['asset_id'],],
	];


  $form_state->setCached(FALSE);

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

    if ($is_edit && ProjectAccessControlHandler::isAdmin()) {
      $form['actions']['delete'] = [
        '#type' => 'submit',
        '#value' => $this->t('Delete'),
        '#submit' => ['::deleteAward'],
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
   * Delete Award.
   */
  public function deleteAward(array &$form, FormStateInterface $form_state) {
    $award_id = $form_state->get('award_id');
    $award = \Drupal::entityTypeManager()->getStorage('asset')->load($award_id);
    $contacts = \Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(
    ['type' => 'contact', 'award' => $award_id]
     );

    try {
      foreach ($contacts as $contact) {
        $contact->delete();
      }
      $award->delete();
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
    $num_contacts = $form_state->get('num_contact_lines');

    $contact_names = [];
    for ($i = 0; $i < $num_contacts; $i++) {
      $contact_name_id = $form['names_fieldset'][$i]['contact_eauth_id']['#value'];
      $contact_names[$i] = $contact_name_id;
    }

    // Check $contact_names array for duplicate values.
    if (!$this->arrayValuesAreUnique($contact_names)) {
      $form_state->setError(
      $form['names_fieldset'],
      $this->t('Each contact eauth id must be unique'),
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
   $is_create = $form_state->get('operation') === 'create';
    $contact_options = $form['names_fieldset'][0]['contact_name']['#options'];

    if ($is_create) {
      $award_submission = [];

      $award_submission['type'] = 'award';

      $award_submission['field_award_agreement_number'] = $form['field_award_agreement_number']['#value'];
      $award_submission['field_award_awardee_org'] = $form['field_award_awardee_org']['#value'];
     
      // Minus 1 because there is an entry with key 'actions'.
      $num_contacts = count($form['names_fieldset']) - 1;

      $contacts = [];

      for ($i = 0; $i < $num_contacts; $i++) {
        $contact_submission['type'] = 'contact';
        $contact_type = $form['names_fieldset'][$i]['contact_type']['#value'];
        $contact_name = $form['names_fieldset'][$i]['contact_name']['#value'];
        $contact_eauth_id = $form['names_fieldset'][$i]['contact_eauth_id']['#value'];
        $contact_email = $form['names_fieldset'][$i]['contact_email']['#value'];

        if ($contact_name === '' || $contact_email === '' || $contact_eauth_id === '' || $contact_type === '' || 
        $contact_name === NULL || $contact_email === NULL || $contact_eauth_id === NULL || $contact_type === NULL) {
          continue;
        }

        $contact_submission['field_contact_email'] = $contact_email;
        $contact_submission['name'] = $contact_name;
        $contact_submission['field_contact_type'] = $contact_type;
        $contact_submission['eauth_id'] = $contact_eauth_id;
        $contact = Asset::create($contact_submission);

        array_push($contacts, $contact);
      }

      $award = Asset::create($award_submission);
      $award->save();

      // TODO something different will need to be done with the contacts here
      // once the acess control behavior is changed
      foreach ($contacts as $contact) {
        $contact->set('award', $award->id());
        $contact->save();
      }
      $form_state->setRedirect('cig_pods.dashboard');
    }
    else {
      $award_id = $form_state->get('award_id');
      $award = \Drupal::entityTypeManager()->getStorage('asset')->load($award_id);

      // Minus 1 because there is an entry with key 'actions'.
      $num_producers = count($form['names_fieldset']) - 1;
      // As above.
      $num_contacts = count($form['names_fieldset']) - 1;

      $contacts = [];
      for ($i = 0; $i < $num_contacts; $i++) {
        $contact_submission['type'] = 'contact';
        $contact_type = $form['names_fieldset'][$i]['contact_type']['#value'];
        $contact_name = $form['names_fieldset'][$i]['contact_name']['#value'];
        $contact_eauth_id = $form['names_fieldset'][$i]['contact_eauth_id']['#value'];
        $contact_email = $form['names_fieldset'][$i]['contact_email']['#value'];

        if ($contact_name === '' || $contact_email === '' || $contact_eauth_id === '' || $contact_type === '' || 
        $contact_name === NULL || $contact_email === NULL || $contact_eauth_id === NULL || $contact_type === NULL) {
          continue;
        }

        $contact_submission['field_contact_email'] = $contact_email;
        $contact_submission['name'] = $contact_name;
        $contact_submission['field_contact_type'] = $contact_type;
        $contact_submission['eauth_id'] = $contact_eauth_id;
        $contact = Asset::create($contact_submission);
        array_push($contacts, $contact);
      }

      foreach ($contacts as $contact) {
        $contact->set('award', $award->id());
        $contact->save();
      }

      $pre_existsing_contacts = $form_state->get('contacts');
      $this->deleteContacts($pre_existsing_contacts);

      $agreement_number = $form_state->getValue('field_award_agreement_number');
      $awardee_organization = $form_state->getValue('field_award_awardee_org');

      $award->set('field_award_agreement_number', $agreement_number);
      $award->set('field_award_awardee_org', $awardee_organization);
      $award->save();
      $form_state->setRedirect('cig_pods.dashboard');
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
   * Delete contacts.
   */
  public function deleteContacts(array $pre_existsing_contacts) {

    try {
      foreach ($pre_existsing_contacts as $contact) {
        $contact->delete();
      }
    }
    catch (\Exception $e) {
      $this
        ->messenger()
        ->addError($e->getMessage());
    }

  }

  /**
   * Ajax callback for adding contact row.
   */
  public function contactRowCallback(array &$form, FormStateInterface $form_state) {
    return $form['names_fieldset'];
  }

  /**
   * Add contact row.
   */
  public function addContactRow(array &$form, FormStateInterface $form_state) {
    $num_contacts = $form_state->get('num_contacts');
    $num_contact_lines = $form_state->get('num_contact_lines');
    $form_state->set('num_contacts', $num_contacts + 1);
    $form_state->set('num_contact_lines', $num_contact_lines + 1);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Remove contact.
   */
  public function removeContact(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $num_line = $form_state->get('num_contact_lines');
    $indexToRemove = str_replace('delete-contact-', '', $trigger['#name']);

    // Remove the fieldset from $form (the easy way)
    unset($form['names_fieldset'][$indexToRemove]);

    // Keep track of removed fields so we can add new fields at the bottom
    // Without this they would be added where a value was removed.
    $removed_contacts = $form_state->get('removed_contacts');
    $removed_contacts[] = $indexToRemove;

    $form_state->set('removed_contacts', $removed_contacts);
    $form_state->set('num_contact_lines', $num_line - 1);

    // Rebuild form_state.
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'project_create_form';
  }

}
