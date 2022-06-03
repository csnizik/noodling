<?php

namespace Drupal\cig_pods\Form;

Use Drupal\Core\Form\FormBase;
Use Drupal\Core\Form\FormStateInterface;

class LabTestProfilesAdminForm extends FormBase {

    public function getSoilHealthExtractionOptions(){
        $shde_options = [];
        $shde_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(
            [
                'vid' => 'bill_test',
            ]
        );

        $shde_keys = array_keys($shde_terms); // {'a':1, 'b': 2} -> ['a','b']
        foreach($shde_keys as $shde_key){
            $term = $shde_terms[$shde_key];
            $sdhe_options[$shde_key] = $term -> getName();        
        }

        return $sdhe_options;
    }

    

    /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state, $options = NULL){

    $form['#attached']['library'][] = 'cig_pods/lab_test_profiles_admin_form';

    $sdhe = $this->getSoilHealthExtractionOptions();

    $form['lab_test_title'] = [
        '#markup' => '<h1>Lab Test Profiles</h1>',
    ]; 

    $form['test_profile_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Test Profile Name'),
        '#required' => TRUE
    ]; 

    $form['laboratory'] = [
			'#type' => 'select',
			'#title' => 'Laboratory',
			'#options' => $sdhe,
			'#required' => TRUE
		];

    $form['aggregate'] = [
			'#type' => 'select',
			'#title' => 'Aggregate Stability Method',
			'#options' => $sdhe,
			'#required' => TRUE
		];

    $form['resp_incubation'] = [
			'#type' => 'select',
			'#title' => 'Respiration Incubation Days',
			'#options' => $sdhe,
			'#required' => TRUE
		];

    $form['resp_detection'] = [
			'#type' => 'select',
			'#title' => 'Respiration Detection Method (unit ppm)',
			'#options' => $sdhe,
			'#required' => TRUE
		];

    $form['electr_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Electroconductivity Method (EC (Unit dS/m))'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['nitrate_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Nitrate-N Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['phos_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Phosphorus Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['potas_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Potassium Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['calc_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Calcium Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['magn_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Magnesium Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['sulf_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Sulfur Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['iron_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Iron Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['mang_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Manganese Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['cop_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Copper Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['zinc_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Zinc Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['boron_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Boron Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['alum_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Aluminum Method (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['moly_method'] = [
        '#type' => 'select',
        '#title' => $this->t('Molybdenum Methon (Unit ppm)'),
        '#options' => $sdhe,
        '#required' => TRUE
    ]; 

    $form['actions']['save'] = [
        '#type' => 'submit',
        '#value' => $this->t('Save'),
    ]; 

    $form['actions']['cancel'] = [
			'#type' => 'button',
			'#value' => $this->t('Cancel'),
			// '#attributes' => array('onClick' => 'window.location.href="/dashboard"'),
    ];


        return $form;

    }

    /**
    * {@inheritdoc}
    */
    public function validateForm(array &$form, FormStateInterface $form_state){
        return;
    }

    /**
    * {@inheritdoc}
    */
    public function getFormId() {
        return 'lab_test_profiles_admin';
    }

    /**
    * {@inheritdoc}
    */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this
            ->messenger()
            ->addStatus($this
            ->t('Form submitted for  @_name', [
            '@_name' => $form['_name']['#value'],
        ]));
    }
}