<?php

namespace Drupal\cig_pods\Form;

use Drupal\cig_pods\ProjectAccessControlHandler;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * PODS Dashboard form for admins and awardees.
 */
class PodsDashboardForm extends PodsFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pods_dashboard_form';
  }

  /**
   * Check access for the form based on zRole.
   */
  public function access() {
    $is_admin = ProjectAccessControlHandler::isAdmin();
    $is_awadee = ProjectAccessControlHandler::isAwardee();
    return AccessResult::allowedIf($is_admin || $is_awadee);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Attach proper CSS to form.
    $form['#attached']['library'][] = 'cig_pods/dashboard';

    // Add title.
    $form['title'] = [
      '#markup' => '<div id="title">Dashboard</div>',
    ];

    // Build the form based on zRole.
    if (ProjectAccessControlHandler::isAdmin()) {
      return $this->buildAdminForm($form, $form_state);
    }
    elseif (ProjectAccessControlHandler::isAwardee()) {
      return $this->buildAwardeeForm($form, $form_state);
    }
    return [];
  }

  /**
   * Build the dashboard form for admins.
   */
  protected function buildAdminForm(array $form, FormStateInterface $form_state) {

    $form['entities_fieldset']['create_new'] = [
      '#type' => 'select',
      '#options' => [
        '' => $this->t('Create New'),
        'create_awardee' => $this->t('Awardee Org'),
        'create_award' => $this->t('Award'),
        'create_project' => $this->t('Project'),
      ],
      '#attributes' => [
        'onchange' => 'this.form.submit();',
      ],
      '#prefix' => '<div id="top-form">',
    ];


    // Create a hidden submit button that will be used when an item is
    // selected from the dropdown. This is necessary because we are using
    // this.form.submit() and if Drupal can't detect the triggering element,
    // it will assume the first button was clicked.
    $form['create'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
      '#name' => 'create',
      '#attributes' => [
        'style' => 'display: none;',
      ],
    ];

    $form['form_body'] = [
      '#markup' => ' <p  id="form-body">Let\'s get started, you can create and manage Awardees, Projects, Lab Test Methods using this tool.</p> ' ,
      '#suffix' => '</div>',
    ];

    $form['form_subtitle'] = [
      '#markup' => '<h2 id="form-subtitle">Manage Assets</h2>',
      '#prefix' => '<div class="bottom-form">',
    ];

    $awardeeEntities = ['project', 'awardee', 'award'];
    $entityCount = [];

    foreach ($awardeeEntities as $bundle) {
      $entities = $this->entityOptions('asset', $bundle);
      $entityCount[] = count($entities);
    }
    
    $form['awardee_org'] = [
      '#type' => 'submit',
      '#value' => $this->t('Awardee Organization(s): @count', ['@count' => $entityCount[1]]),
      '#name' => 'awardee',
    ];
  
    $form['awardee_award'] = [
      '#type' => 'submit',
      '#value' => $this->t('Award(s): @count', ['@count' => $entityCount[2]]),
      '#name' => 'award',
    ];      

    $form['awardee_proj'] = [
      '#type' => 'submit',
      '#value' => $this->t('Project(s): @count', ['@count' => $entityCount[0]]),
      '#name' => 'project',
    ];

    return $form;
  }

  /**
   * Build the dashboard form for awardees.
   */
  public function buildAwardeeForm(array $form, FormStateInterface $form_state) {
    $creation_options = [];
    if(ProjectAccessControlHandler::isProjectManager()){
      $creation_options = [
        '' => $this->t('Create New'),
        'management' => $this->t('-----Managment-----'),
        'create_project' => $this->t('Project'),
        'assets' => $this->t('-----Assets-----'),
        'create_producer' => $this->t('Producer'),
        'create_lab_testing_method' => $this->t('Methods'),
        'create_soil_health_management_unit' => $this->t('SHMU'),
        'create_soil_health_sample' => $this->t('Soil Sample'),
        'create_lab_result' => $this->t('Soil Test Result'),
        'create_operation' => $this->t('Operation'),
        'create_irrigation' => $this->t('Irrigation Test'),
        'assesments' => $this->t('-----Assessments-----'),
        'create_field_assessment' => $this->t('CIFSH Assessment'),
        'create_rangeland_assessment' => $this->t('IIRH Assessment'),
        'create_pasture_assessment' => $this->t('PCS Assessment'),
        'create_pasture_health_assessment' => $this->t('DIPH Assessment'),
      ];
    }else{
      $creation_options = [
        '' => $this->t('Create New'),
        'assets' => $this->t('-----Assets-----'),
        'create_producer' => $this->t('Producer'),
        'create_lab_testing_method' => $this->t('Methods'),
        'create_soil_health_management_unit' => $this->t('SHMU'),
        'create_soil_health_sample' => $this->t('Soil Sample'),
        'create_lab_result' => $this->t('Soil Test Result'),
        'create_operation' => $this->t('Operation'),
        'create_irrigation' => $this->t('Irrigation Test'),
        'assesments' => $this->t('-----Assessments-----'),
        'create_field_assessment' => $this->t('CIFSH Assessment'),
        'create_rangeland_assessment' => $this->t('IIRH Assessment'),
        'create_pasture_assessment' => $this->t('PCS Assessment'),
        'create_pasture_health_assessment' => $this->t('DIPH Assessment'),
      ];
    }

    $form['entities_fieldset']['create_new'] = [
      '#type' => 'select',
      '#options' => $creation_options,
      '#attributes' => [
        'onchange' => 'this.form.submit();',
      ],
      '#prefix' => ' <div id="top-form"> 
   ',
    ];

  
    
    // Create a hidden submit button that will be used when an item is
    // selected from the dropdown. This is necessary because we are using
    // this.form.submit() and if Drupal can't detect the triggering element,
    // it will assume the first button was clicked.
    $form['create'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
      '#name' => 'create',
      '#attributes' => [
        'style' => 'display: none;',
      ],
    ];

    $form['form_body'] = [
      '#markup' => '<p id="form-body">Let\'s get started, you can create and manage Producers, Soil Health Management Units (SHMU), Soil Samples, Lab Test Methods, and Operations using this tool.</p>',
      '#suffix' => '</div>', 
    ];
    $form['form_body2'] = [
      '#prefix' => '<div class="middle-form">',
      '#markup' => '
      <div class="container">
        <div class="row">
          <h2 id="form-subtitle">Get Started</h2> 
          <p>Welcome to the Producer Operations Data Service. In order to submit your (or your partner organization\'s) data you will need to do so in a particular order.</p>
        </div>

        <div id="grape_row" class="row">
          <span id="grape" onclick="showInfo2()">View Detailed Instructions</span> 
        </div>

        <div class="row">
          <div id="text2" hidden>
            <p id="collapse_text">
              <h6 class="collapse_header">1. Create "Producer(s)"</h6>
              Start by entering the basic data for your Producer or Producers, the individual or 
              organization responsible for producing the data and agricultural commodities relevant to your grant.
              <br><br>

              <h6 class="collapse_header">2. Create "SHMU(s)"</h6>
              Once you have created a Producer, you should then identify the "Soil Health Management Units" or SHMUs that they are performing their trial across. 
              These may, or may not, align with their fields based on the experimental design of your trial.
              <br><br>

              <h6 class="collapse_header">3. Create "Soil Sample(s)", "Irrigation(s)", "Operation(s)", and Add Relevant Assessments</h6>
              Once your SHMU or SHMUs are created, many new entities open up. 
              You will want to submit details about Soil Samples, Irrigation (Water Testing), Operations (Agricultural Activities on the ground), and the relevant in-field assessments.
              <br><br>

              <h6 class="collapse_header">4. Create "Method(s)" and "Soil Test Result(s)"</h6>
              You will also need to create some (Lab Test) Methods that define how your soil samples will be tested. 
              Once you have a method, you can then start cataloguing soil test results. One method can be applied across many sets of results, 
              so if you have a consistent set of tests being performed you do not need to create multiple Methods!
              <br><br>

              <i>If you have any further questions on the use of PODS or feedback on the sites flow and functionality please contact your program officer for more details.</i>
            </p> 
          </div>
        </div>
      </div>',
   '#suffix' => '</div>',
    ];

    if(ProjectAccessControlHandler::isProjectManager()){
      $form['form_manager_section'] = [
        '#markup' => '<div> <h2 id="form-subtitle3">Award and Project Management<span class="small-question" title="This section is for Project Managers to manage the people who are a part of which project."><sup>?</sup></span></div></h2>',
        '#prefix' => '<div class="bottom-form">',
      ];
      
      $form['awardee_award'] = [
        '#type' => 'submit',
        '#value' => $this->t('Award(s): @count', ['@count' => ProjectAccessControlHandler::projectManagerEntityCounts(FALSE)]),
        '#name' => 'project_manager_award',
      ];
      
      $form['awardee_project'] = [
        '#type' => 'submit',
        '#value' => $this->t('Project(s): @count', ['@count' => ProjectAccessControlHandler::projectManagerEntityCounts(TRUE)]),
        '#name' => 'project_manager_project',
        '#suffix' => '</div>',
      ];
    }

    $form['form_subtitle'] = [
      '#markup' => '<div> <h2 id="form-subtitle">Manage Assets<span class="small-question" title="Assets are identifiable persons, field samples, management actions and operations performed within the SHMU."><sup>?</sup></span></div></h2>',
      '#prefix' => '<div class="bottom-form">',
    ];

    $awardeeEntities = [
      'project',
      'producer',
      'soil_health_sample',
      'lab_result',      
      'soil_health_management_unit',
      'lab_testing_method',
      'operation',
      'irrigation',
      'soil_health_management_unit',
      'field_assessment',
      'range_assessment',
      'pasture_assessment',
      'pasture_health_assessment',
    ];

    $entityCount = [];

    foreach ($awardeeEntities as $bundle) {
      $entities = $this->entityOptions('asset', $bundle);
      $entityCount[$bundle] = count($entities);
    }

    // If no projects are assigned, display a warning.
    if (empty($entityCount['project'])) {
      $this->messenger()->addWarning($this->t('You are not currently assigned to any projects. You must be assigned as a project contact in order to create or edit records.'));
    }


    $form['awardee_producer'] = [
      '#type' => 'submit',
      '#value' => $this->t('Producer(s): @count', ['@count' => $entityCount['producer']]),
      '#name' => 'producer',
    ];

    $form['awardee_lab'] = [
      '#type' => 'submit',
      '#value' => $this->t('Method(s):  @count', ['@count' => $entityCount['lab_testing_method']]),
      '#name' => 'lab_testing_method',
    ];

    $form['awardee_soil_health_management_unit'] = [
      '#type' => 'submit',
      '#value' => $this->t('SHMU(s): @count', ['@count' => $entityCount['soil_health_management_unit']]),
      '#name' => 'soil_health_management_unit',
      '#attributes' => array('title'=>t('Soil Health Management Unit (SHMU) is one or more planning land units with similar soil type, land use, and management that can vary in size or acreage depending on soil texture, topography, and cropping system. SHMU is like a conservation management unit but designed to assess soil health status and potential limitations on soil health indicators.')),
    ];

    $form['awardee_soil_health_sample'] = [
      '#type' => 'submit',
      '#value' => $this->t('Soil Sample(s): @count', ['@count' => $entityCount['soil_health_sample']]),
      '#name' => 'soil_health_sample',
    ];

    $form['awardee_lab_result'] = [
      '#type' => 'submit',
      '#value' => $this->t('Soil Test Result(s): @count', ['@count' => $entityCount['lab_result']]),
      '#name' => 'lab_result','#attributes' => array('title'=>t('Enter the method(s) used by the laboratory to conduct the soil tests')),
    ];
    
    $form['awardee_operation'] = [
      '#type' => 'submit',
      '#value' => $this->t('Operation(s): @count', ['@count' => $entityCount['operation']]),
      '#name' => 'operation',
    ];

    $form['awardee_irrigation'] = [
      '#type' => 'submit',
      '#value' => $this->t('Irrigation Test(s): @count', ['@count' => $entityCount['irrigation']]),
      '#name' => 'irrigation',
      '#suffix' => '</div>',
    ];

    $form['form_subtitle2'] = [
      '#markup' => '<h2 id="form-subtitle2">Manage Assessments<span class="small-question" title="Assessments provide an evaluation of resources within the SHMU at one or more points in time."><sup>?</sup></span></h2>',
      '#prefix' => '<div class="bottom-form">',
    ];

    $form['awardee_in_field_assessment'] = [
      '#type' => 'submit',
      '#value' => $this->t('CIFSH Assessment(s): @count', ['@count' => $entityCount['field_assessment']]),
      '#name' => 'field_assessment',
    ];

    $form['awardee_rangeland_assessment'] = [
      '#type' => 'submit',
      '#value' => $this->t('IIRH Assessment(s): @count', ['@count' => $entityCount['range_assessment']]),
      '#name' => 'rangeland_assessment',
    ];

    $form['awardee_pasture_assessment'] = [
      '#type' => 'submit',
      '#value' => $this->t('PCS Assessment(s): @count', ['@count' => $entityCount['pasture_assessment']]),
      '#name' => 'pasture_assessment',
    ];

    $form['awardee_pasture_health_assessment'] = [
      '#type' => 'submit',
      '#value' => $this->t('DIPH Assessment(s): @count', ['@count' => $entityCount['pasture_health_assessment']]),
      '#name' => 'pasture_health_assessment',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Define redirect paths based on asset type.
    $redirects = [

      // Admin asset creation paths:
      'create_project' => '/create/project',
      'create_awardee' => '/create/awardee_org',
      'create_award' => '/create/award',

      // Admin asset list paths:
      'project' => '/assets/project',
      'awardee' => '/assets/awardee',
      'award' => '/assets/award',

      // Awardee asset creation paths:
      'create_producer' => '/create/producer',
      'create_soil_health_management_unit' => '/create/shmu',
      'create_soil_health_sample' => '/create/soil_health_sample',
      'create_field_assessment' => '/create/field_assessment',
      'create_rangeland_assessment' => '/create/range_assessment',
      'create_pasture_assessment' => '/create/pasture_assessment',
      'create_pasture_health_assessment' => '/create/pasture_health_assessment',
      'create_lab_result' => '/create/lab_results',
      'create_lab_testing_method' => '/create/lab_testing_method',
      'create_irrigation' => '/create/irrigation',
      'create_operation' => '/create/operation',

      // Awardee asset list paths:
      'project_manager_project' => '/assets/project_manager/project',
      'project_manager_award' => '/assets/project_manager/award',
      'producer' => '/assets/producer',
      'soil_health_management_unit' => '/assets/soil_health_management_unit',
      'soil_health_sample' => '/assets/soil_health_sample',
      'field_assessment' => '/assets/field_assessment',
      'rangeland_assessment' => '/assets/range_assessment',
      'pasture_assessment' => '/assets/pasture_assessment',
      'pasture_health_assessment' => '/assets/pasture_health_assessment',
      'lab_result' => '/assets/lab_result',
      'lab_testing_method' => '/assets/lab_testing_method',
      'irrigation' => '/assets/irrigation',
      'operation' => '/assets/operation',
    ];

    // Get the triggering element name and redirect accordingly.
    // This will either be "create" or a specific asset type. If it is "create"
    // then we know that the "Create new" select box was changed, and we can
    // get the submitted value and redirect to the asset creation form.
    // Otherwise, one of the asset type buttons was clicked, so we redirect to
    // the asset list page.
    $triggering_element = $form_state->getTriggeringElement();
    if (!empty($triggering_element['#name'])) {
      $name = $triggering_element['#name'];
      if ($name == 'create') {
        $type = $form_state->getValue('create_new');
        if (isset($redirects[$type])) {
          $form_state->setRedirectUrl(Url::fromUri('internal:' . $redirects[$type]));
        }
      }
      elseif (isset($redirects[$name])) {
        $form_state->setRedirectUrl(Url::fromUri('internal:' . $redirects[$name]));
      }
    }
  }
}