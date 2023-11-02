<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the GHG Benefits Alternate Modeled log type.
 *
 * @LogType(
 * id = "ghg_benefits_alternate_modeled",
 * label = @Translation("GHGBenefitsAlternateModeled"),
 * )
 */
class GHGBenefitsAlternateModeled extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'g_benefits_alternate_modeled_fiscal_year' => [
        'type' => 'string',
        'label' => 'Federal Fiscal Year of report submission',
        'description' => 'Federal Fiscal Year of report submission',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_alternate_modeled_fiscal_quarter' => [
        'type' => 'string',
        'label' => 'Federal Fiscal Quarter of report submission',
        'description' => 'Federal Fiscal Quarter of report submission',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_alternate_modeled_field_id' => [
        'type' => 'entity_reference',
        'label' => 'Field ID',
        'description' => 'Field ID',
		    'target_type' => 'asset',
		    'target_bundle' => 'csc_field_enrollment',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'project_id' => [
        'type' => 'entity_reference',
        'label' => 'Project ID',
        'description' => 'Project ID',
        'target_type' => 'asset',
        'target_bundle' => 'csc_project_summary',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_alternate_modeled_commodity_type' => [
        'type' => 'entity_reference',
        'label' => 'Field Summary Commodity Type',
        'description' => 'Field Summary Commodity Type',
		    'target_type' => 'taxonomy_term',
		    'target_bundle' => 'commodity_term',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_alternate_modeled_practice_type' => [
        'type' => 'entity_reference',
        'label' => 'Practice Type',
        'description' => 'Practice Type',
		    'target_type' => 'taxonomy_term',
		    'target_bundle' => 'practice_type',
        'required' => TRUE,
        'multiple' => TRUE,
      ],
      'g_benefits_alternate_modeled_ghg_model' => [
        'type' => 'entity_reference',
        'label' => 'Result GHG Model',
        'description' => 'Result GHG Model',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'ghg_model',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_alternate_modeled_ghg_model_other' => [
        'type' => 'string',
        'label' => 'Other GHG Model',
        'description' => 'Other GHG Model',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_alternate_modeled_model_start_date' => [
        'type' => 'timestamp',
        'label' => 'Model Start Date',
        'description' => 'Model Start Date',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_alternate_modeled_model_end_date' => [
        'type' => 'timestamp',
        'label' => 'Model End Date',
        'description' => 'Model End Date',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'g_benefits_alternate_modeled_ghg_benefits_estimated' => [
        'type' => 'fraction',
        'label' => 'Total GHG Benefits Estimated',
        'description' => 'Total GHG Benefits Estimated',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'g_benefits_alternate_modeled_carbon_stock_estimated' => [
        'type' => 'fraction',
        'label' => 'Total Carbon Stock Estimated',
        'description' => 'Total Carbon Stock Estimated',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'g_benefits_alternate_modeled_co2_estimated' => [
        'type' => 'fraction',
        'label' => 'Total CO2 Estimated',
        'description' => 'Total CO2 Estimated',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'g_benefits_alternate_modeled_ch4_estimated' => [
        'type' => 'fraction',
        'label' => 'Total CH4 Estimated',
        'description' => 'Total CH4 Estimated',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'g_benefits_alternate_modeled_n2o_estimated' => [
        'type' => 'fraction',
        'label' => 'Total N2O Estimated',
        'description' => 'Total N2O Estimated',
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