<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Combustion System Improvement log type.
 *
 * @LogType(
 * id = "csc_combustion_sys_improvement",
 * label = @Translation("Combustion System Improvement Log"),
 * )
 */
class CombustionSystemImprovement extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p372_prior_fuel_type' => [
        'type' => 'entity_reference',
        'label' => 'Combustion System Improvement Fuel type before installation',
        'description' => 'Combustion System Improvement Fuel type before installation',
		    'target_type' => 'taxonomy_term',
		    'target_bundle' => 'fuel_type',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p372_prior_fuel_type_other' => [
        'type' => 'string',
        'label' => 'Combustion System Improvement Other fuel type before installation',
        'description' => 'Combustion System Improvement Other fuel type before installation',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p372_prior_fuel_amount' => [
        'type' => 'fraction',
        'label' => 'Combustion System Improvement Fuel amount before installation',
        'description' => 'Combustion System Improvement Fuel amount before installation',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p372_prior_fuel_amount_unit' => [
        'type' => 'list_string',
        'label' => 'Combustion System Improvement Fuel amount unit before installation',
        'description' => 'Combustion System Improvement Fuel amount unit before installation',
		    'allowed_values' => [
          'Cubic feet (natural gas)' => t(string: 'Cubic feet (natural gas)'),
          'Gallons (diesel, gasoline, propane, LPG, kerosene)' => t(string: 'Gallons (diesel, gasoline, propane, LPG, kerosene)'),
          'Kilowatt-hours (electricity)' => t(string: 'Kilowatt-hours (electricity)'),
          'Pounds (wood, coal)' => t(string: 'Pounds (wood, coal)'),
          'Other(Specify)' => t(string: 'Other(Specify)'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p372_pri_fuel_amnt_unit_otr' => [
        'type' => 'string',
        'label' => 'Combustion System Improvement Other fuel amount unit before installation',
        'description' => 'Combustion System Improvement Other fuel amount unit before installation',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p372_fuel_type_after' => [
        'type' => 'entity_reference',
        'label' => 'Combustion System Improvement Fuel type after installation',
        'description' => 'Combustion System Improvement Fuel type after installation',
        'target_type' => 'taxonomy_term',
		    'target_bundle' => 'fuel_type',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p372_fuel_type_after_other' => [
        'type' => 'string',
        'label' => 'Combustion System Improvement Other fuel type after installation',
        'description' => 'Combustion System Improvement Other fuel type after installation',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p372_fuel_amount_after' => [
        'type' => 'fraction',
        'label' => 'Combustion System Improvement Fuel amount after installation',
        'description' => 'Combustion System Improvement Fuel amount after installation',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p372_fuel_amount_unit_after' => [
        'type' => 'list_string',
        'label' => 'Combustion System Improvement Fuel amount unit after installation',
        'description' => 'Combustion System Improvement Fuel amount unit after installation',
		    'allowed_values' => [
          'Cubic feet (natural gas)' => t(string: 'Cubic feet (natural gas)'),
          'Gallons (diesel, gasoline, propane, LPG, kerosene)' => t(string: 'Gallons (diesel, gasoline, propane, LPG, kerosene)'),
          'Kilowatt-hours (electricity)' => t(string: 'Kilowatt-hours (electricity)'),
          'Pounds (wood, coal)' => t(string: 'Pounds (wood, coal)'),
          'Other(Specify)' => t(string: 'Other(Specify)'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p372_fuel_amnt_unit_aft_otr' => [
        'type' => 'string',
        'label' => 'Combustion System Improvement Other fuel amount unit after installation',
        'description' => 'Combustion System Improvement Other fuel amount unit after installation',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_project_id' => [
        'type' => 'entity_reference',
        'label' => 'Project ID',
        'description' => 'Project ID',
        'target_type' => 'asset',
        'target_bundle' => 'project_summary',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_field_id' => [
        'type' => 'entity_reference',
        'label' => 'Field ID',
        'description' => 'Field ID',
		    'target_type' => 'asset',
		    'target_bundle' => 'field_enrollment',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
    ];

    $farmFieldFactory = new FarmFieldFactory();

    foreach ($field_info as $name => $info) {
      $fields[$name] = $farmFieldFactory->bundleFieldDefinition($info)
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
    }

    return $fields;

  }

}
