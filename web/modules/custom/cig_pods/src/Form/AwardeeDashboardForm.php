<?php

namespace Drupal\cig_pods\Form;

Use Drupal\Core\Form\FormBase;
Use Drupal\Core\Form\FormStateInterface;

class AwardeeDashboardForm extends FormBase {


   /**
   * {@inheritdoc}
   */
	public function buildForm(array $form, FormStateInterface $form_state, $options = NULL){

    $form['entities_fieldset'][$i]['create_new'] = [
				'#type' => 'select',
				'#options' => [
				  '' => $this
					->t('Create New'),
				  'pr' => $this
					->t('Producer(s)'),
				  'awo' => $this
					->t('Awardee Org'),
				  'proj' => $this
					->t('Project'),
                  'ltm' => $this
					->t('Lab Test Method'),
                ],
				'attributes' => [
					'class' => 'something'
				],
				'#prefix' => ($num_lines > 1) ? '<div class="inline-components-short">' : '<div class="inline-components">',
		  		'#suffix' => '</div>',
			];

    $form['form_body'] = [
        '#markup' => '<p id="form-body">Let\'s get started, you can create and manage Awardees, Projects, Lab Test Methods and Producers using this tool.</p>'
    ]; 

    $form['form_subtitle'] = [
        '#markup' => '<h2 id="form-subtitle">Manage Assets</h2>'
    ]; 

     $awardeeEntities = array('project', 'awardee','producer', 'soil_health_demo_trial' );
       $entityCount = array();

      for($i = 0; $i < count($awardeeEntities); $i++){
        $query = \Drupal::entityQuery('asset')->condition('type',$awardeeEntities[i]);
        array_push($entityCount, $query->count()->execute());
      }

    $form['awardee_proj'] = [
      '#type' => 'button',
      '#value' => $this->t('Projects(s): '.$entityCount[0]),
      '#submit' => ['::projectRedirect'],
    ]; 

    $form['awardee_org'] = [
      '#type' => 'button',
      '#value' => $this->t('Awardee Organization(s): '.$entityCount[1]),
      '#submit' => ['::orgRedirect'],
    ]; 
	

    $form['awardee_prod'] = [  
      '#type' => 'button',
      '#value' => $this->t('Producer(s): '.$entityCount[2]),
      '#submit' => ['::producerRedirect'],
    ]; 

		$form['awardee_lab'] = [
      '#type' => 'submit',
      '#value' => $this->t('Lab Test Method(a): '.$entityCount[3]),
      '#submit' => ['::labRedirect'],
    ]; 
		
		return $form;
	}

  private function pageRedirect (FormStateInterface $form_state, string $path) {
     $match = [];
    $path2 =  $path;
    $router = \Drupal::service('router.no_access_checks');

    try {
      $match = $router->match($path); 
    }
    catch (\Exception $e) {
      // The route using that path hasn't been found,
      // or the HTTP method isn't allowed for that route.
    }
   $form_state->setRedirect($match["_route"]);
  }

  public function projectRedirect (array &$form, FormStateInterface $form_state) {
   pageRedirect($form_state, "/assets/project");
}
public function orgRedirect (array &$form, FormStateInterface $form_state) {
   pageRedirect($form_state, "/assets/awardee");
}
public function producerRedirect (array &$form, FormStateInterface $form_state) {
   pageRedirect($form_state, "/assets/producer");
}
  public function labRedirect (array &$form, FormStateInterface $form_state) {
   pageRedirect($form_state, "/assets/lab_testing_profile");
}

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state){
    echo "<script>console.log('validate form' );</script>";
    //window.location.assign("/create/producer");
	return true;
}

  /**
   * {@inheritdoc}
   */
   public function submitForm(array &$form, FormStateInterface $form_state) {



	$this
	  ->messenger()
	  ->addStatus($this
	  ->t('Form submitted for entities_fieldset @entities_fieldset', [
	  '@entities_fieldset' => $form['entities_fieldset']['#value'],
	]));
   }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'awardee_dashboard_form';
  }
}