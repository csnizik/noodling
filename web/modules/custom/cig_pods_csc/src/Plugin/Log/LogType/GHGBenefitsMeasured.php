<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the GHG Benefits Measured log type.
 *
 * @LogType(
 * id = "csc_ghg_benefits_measured",
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
      'csc_g_bene_msrd_fiscal_year' => [
        'type' => 'string',
        'label' => 'Federal Fiscal Year of report submission',
        'description' => 'Federal Fiscal Year of report submission',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_fiscal_quarter' => [
        'type' => 'string',
        'label' => 'Federal Fiscal Quarter of report submission',
        'description' => 'Federal Fiscal Quarter of report submission',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_fld_id' => [
        'type' => 'entity_reference',
        'label' => 'Field ID',
        'description' => 'Field ID',
		    'target_type' => 'asset',
		    'target_bundle' => 'csc_field_enrollment',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_project_id' => [
        'type' => 'entity_reference',
        'label' => 'Project ID',
        'description' => 'Project ID',
        'target_type' => 'asset',
        'target_bundle' => 'csc_project_summary',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_ghg_msrt_mt' => [
        'type' => 'entity_reference',
        'label' => 'GHG measurement method',
        'description' => 'GHG measurement method',
		    'target_type' => 'taxonomy_term',
		    'target_bundle' => 'ghg_measurement_method',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_ghg_msrt_mt_otr' => [
        'type' => 'string',
        'label' => 'Other GHG measurement method',
        'description' => 'Other GHG measurement method',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_lab_name' => [
        'type' => 'string',
        'label' => 'Lab Name',
        'description' => 'Lab Name',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_msrt_start_date' => [
        'type' => 'timestamp',
        'label' => 'Measurement Start Date',
        'description' => 'Measurement Start Date',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_msrt_end_date' => [
        'type' => 'timestamp',
        'label' => 'Measurement End Date',
        'description' => 'Measurement End Date',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_g_bene_msrd_total_co2_rd' => [
        'type' => 'fraction',
        'label' => 'Total CO2 reduction',
        'description' => 'Total CO2 reduction',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_g_bene_msrd_t_fld_co2_stock' => [
        'type' => 'fraction',
        'label' => 'Total Field Carbon Stock',
        'description' => 'Total Field Carbon Stock',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_g_bene_msrd_total_ch4_rd' => [
        'type' => 'fraction',
        'label' => 'Total CH4 reduction',
        'description' => 'Total CH4 reduction',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_total_n2o_rd' => [
        'type' => 'fraction',
        'label' => 'Total N20 reduction',
        'description' => 'Total N20 reduction',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_g_bene_msrd_sl_sp_rs' => [
        'type' => 'fraction',
        'label' => 'Soil Sample Result',
        'description' => 'Soil Sample Result',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_sl_sp_rs_ut' => [
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
      'csc_g_bene_msrd_sl_sp_rs_ut_otr' => [
        'type' => 'string',
        'label' => 'Soil Sample Result Unit Other',
        'description' => 'Soil Sample Result Unit Other',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_g_bene_msrd_msrt_type' => [
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
      'csc_g_bene_msrd_msrt_type_otr' => [
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