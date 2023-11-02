<?php

namespace Drupal\cig_pods_csc\Plugin\Asset\AssetType;

use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the CIG csc_project asset type.
 *
 * @AssetType(
 * id = "csc_project_summary",
 * label = @Translation("Awardee"),
 * )
 */
class ProjectSummary extends FarmAssetType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p_summary_fiscal_year' => [
        'type' => 'string',
        'label' => 'Federal Fiscal Year of Report Submission',
        'description' => 'Federal Fiscal Year of Report Submission',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_fiscal_quarter' => [
        'type' => 'string',
        'label' => 'Federal Fiscal Quarter of Report Submission',
        'description' => 'Federal Fiscal Quarter of Report Submission',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_commodity_type' => [
        'type' => 'entity_reference',
        'label' => 'Project Summary Commodity Type',
        'description' => 'Project Summary Commodity Type',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'commodity_term',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_p_summary_commodity_sales' => [
        'type' => 'boolean',
        'label' => 'Commodity Sales',
        'description' => 'Commodity Sales',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_p_summary_farms_enrolled' => [
        'type' => 'boolean',
        'label' => 'Farms Enrolled',
        'description' => 'Farms Enrolled',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_p_summ_ghg_calculation_mthds' => [
        'type' => 'entity_reference',
        'label' => 'Project Summary GHG Calculation Methods',
        'description' => 'Project Summary GHG Calculation Methods',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'ghg_calculation_methods',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summ_ghg_cum_calculation' => [
        'type' => 'entity_reference',
        'label' => 'Project Summary GHG Cumulative Calculation',
        'description' => 'Project Summary GHG Cumulative Calculation',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'ghg_cumulative_calculation',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_ghg_benefits' => [
        'type' => 'fraction',
        'label' => 'Project Summary Cumulative GHG Benefits',
        'description' => 'Project Summary Cumulative GHG Benefits',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summ_cum_carbon_stack' => [
        'type' => 'fraction',
        'label' => 'Project Summary Cumulative Carbon Stack',
        'description' => 'Project Summary Cumulative Carbon Stack',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summ_cum_co2_benefit' => [
        'type' => 'fraction',
        'label' => 'Project Summary Cumulative CO2 Benefit',
        'description' => 'Project Summary Cumulative CO2 Benefit',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summ_cum_ch4_benefit' => [
        'type' => 'fraction',
        'label' => 'Project Summary Cumulative CH4 Benefit',
        'description' => 'Project Summary Cumulative CH4 Benefit',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summ_cum_n2o_benefit' => [
        'type' => 'fraction',
        'label' => 'Project Summary Cumulative N2O Benefit',
        'description' => 'Project Summary Cumulative N2O Benefit',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_offsets_produced' => [
        'type' => 'fraction',
        'label' => 'Project Summary Offsets Produced',
        'description' => 'Project Summary Offsets Produced',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_offsets_produced' => [
        'type' => 'fraction',
        'label' => 'Project Summary Offsets Produced',
        'description' => 'Project Summary Offsets Produced',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_offsets_sale' => [
        'type' => 'string',
        'label' => 'Project Summary Offsets Sale',
        'description' => 'Project Summary Offsets Sale',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_offsets_price' => [
        'type' => 'fraction',
        'label' => 'Project Summary Offsets Price',
        'description' => 'Project Summary Offsets Price',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_insets_produced' => [
        'type' => 'fraction',
        'label' => 'Project Summary Insets Produced',
        'description' => 'Project Summary Insets Produced',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_cost_on_farm' => [
        'type' => 'fraction',
        'label' => 'Project Summary Cost Of On-Farm TA',
        'description' => 'Project Summary Cost Of On-Farm TA',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summary_mmrv_cost' => [
        'type' => 'fraction',
        'label' => 'Project Summary MMRV Cost',
        'description' => 'Project Summary MMRV Cost',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summ_ghg_monitoring_mthd' => [
        'type' => 'entity_reference',
        'label' => 'Project Summary GHG Monitoring Method',
        'description' => 'Project Summary GHG Monitoring Method',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'ghg_monitoring_method',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summ_ghg_reporting_mthd' => [
        'type' => 'entity_reference',
        'label' => 'Project Summary GHG Reporting Method',
        'description' => 'Project Summary GHG Reporting Method',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'ghg_reporting_method',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_p_summ_ghg_verification_mthd' => [
        'type' => 'entity_reference',
        'label' => 'Project Summary GHG Verification Method',
        'description' => 'Project Summary GHG Verification Method',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'ghg_verification_method',
        'required' => TRUE ,
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
