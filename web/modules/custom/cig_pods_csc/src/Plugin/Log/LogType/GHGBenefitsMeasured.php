<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the GHG Benefits Measured log type.
 *
 * @LogType(
 * id = "ghg_benefits_measured",
 * label = @Translation("GHGBenefitsMeasured"),
 * )
 */
class GHGBenefitsMeasured extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'g_benefits_measured_fiscal_year' => [
        'type' => 'string',
        'label' => 'Federal Fiscal Year of report submission',
        'description' => 'Federal Fiscal Year of report submission',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_fiscal_quarter' => [
        'type' => 'string',
        'label' => 'Federal Fiscal Quarter of report submission',
        'description' => 'Federal Fiscal Quarter of report submission',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_field_id' => [
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
      'g_benefits_measured_ghg_measurement_method' => [
        'type' => 'entity_reference',
        'label' => 'GHG measurement method',
        'description' => 'GHG measurement method',
		    'target_type' => 'taxonomy_term',
		    'target_bundle' => 'ghg_measurement_method',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_ghg_measurement_method_other' => [
        'type' => 'string',
        'label' => 'Other GHG measurement method',
        'description' => 'Other GHG measurement method',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_lab_name' => [
        'type' => 'string',
        'label' => 'Lab Name',
        'description' => 'Lab Name',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_measurement_start_date' => [
        'type' => 'timestamp',
        'label' => 'Measurement Start Date',
        'description' => 'Measurement Start Date',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_measurement_end_date' => [
        'type' => 'timestamp',
        'label' => 'Measurement End Date',
        'description' => 'Measurement End Date',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'g_benefits_measured_total_co2_reduction' => [
        'type' => 'fraction',
        'label' => 'Total CO2 reduction',
        'description' => 'Total CO2 reduction',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'g_benefits_measured_total_field_carbon_stock' => [
        'type' => 'fraction',
        'label' => 'Total Field Carbon Stock',
        'description' => 'Total Field Carbon Stock',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'g_benefits_measured_total_ch4_reduction' => [
        'type' => 'fraction',
        'label' => 'Total CH4 reduction',
        'description' => 'Total CH4 reduction',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_total_n2o_reduction' => [
        'type' => 'fraction',
        'label' => 'Total N20 reduction',
        'description' => 'Total N20 reduction',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'g_benefits_measured_soil_sample_result' => [
        'type' => 'fraction',
        'label' => 'Soil Sample Result',
        'description' => 'Soil Sample Result',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_soil_sample_result_unit' => [
        'type' => 'list_string',
        'label' => 'Soil Sample Result Unit',
        'description' => 'Soil Sample Result Unit',
        'allowed_values' => [
          'Grams' => t(string: 'Grams'),
          'Grams per cubic centimeter' => t(string: 'Grams per cubic centimeter'),
          'Percent' => t(string: 'Percent'),
          'Ppm' => t(string: 'Ppm'),
          'Other' => t(string: 'Other'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_soil_sample_result_unit_other' => [
        'type' => 'string',
        'label' => 'Soil Sample Result Unit Other',
        'description' => 'Soil Sample Result Unit Other',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_measurement_type' => [
        'type' => 'list_string',
        'label' => 'Measurement Type',
        'description' => 'Measurement Type',
        'allowed_values' => [
          'Bulk Density' => t(string: 'Bulk Density'),
          'Organic Matter' => t(string: 'Organic Matter'),
          'Total Organic Carbon' => t(string: 'Total Organic Carbon'),
          'Other' => t(string: 'Other'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'g_benefits_measured_measurement_type_other' => [
        'type' => 'string',
        'label' => 'Measurement Type Other',
        'description' => 'Measurement Type Other',
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