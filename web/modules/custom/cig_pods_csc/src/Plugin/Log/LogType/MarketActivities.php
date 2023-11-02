<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Market Activities log type.
 *
 * @LogType(
 * id = "csc_market_activities",
 * label = @Translation("MarketActivities"),
 * )
 */
class MarketActivities extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_m_activities_commodity_type' => [
        'type' => 'entity_reference',
        'label' => 'Market Activities Commodity Type',
        'description' => 'Market Activities Commodity Type',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'commodity_term',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_mktng_chnl_type' => [
        'type' => 'entity_reference',
        'label' => 'Market Activities Marketing Channel Type',
        'description' => 'Market Activities Marketing Channel Type',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'marketing_channel_type',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_mktng_chnl_type_otr' => [
        'type' => 'string',
        'label' => 'Market Activities Other marketing channel type',
        'description' => 'Market Activities Other marketing channel type',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_number_of_buyers' => [
        'type' => 'fraction',
        'label' => 'Market Activities Number of buyers',
        'description' => 'Market Activities Number of buyers',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_activities_buyer_names' => [
        'type' => 'string',
        'label' => 'Market Activities Names of buyers',
        'description' => 'Market Activities Names of buyers',
        'required' => TRUE,
        'multiple' => TRUE,
      ],
      'csc_m_act_mktng_chnl_geography' => [
        'type' => 'entity_reference',
        'label' => 'Market Activities Marketing channel geography',
        'description' => 'Market Activities Marketing channel geography',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'marketing_channel_geography',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_activities_value_sold' => [
        'type' => 'fraction',
        'label' => 'Market Activities Value sold',
        'description' => 'Market Activities Value sold',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_activities_volume_sold' => [
        'type' => 'fraction',
        'label' => 'Market Activities Volume sold',
        'description' => 'Market Activities Volume sold',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_volume_sold_unit' => [
        'type' => 'entity_reference',
        'label' => 'Market Activities Volume sold unit',
        'description' => 'Market Activities Volume sold unit',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'volume_sold_unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_volume_unit_otr' => [
        'type' => 'string',
        'label' => 'Market Activities Other volume sold unit',
        'description' => 'Market Activities Other volume sold unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_activities_price_premium' => [
        'type' => 'fraction',
        'label' => 'Market Activities Price premium',
        'description' => 'Market Activities Price premium',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_price_premium_unit' => [
        'type' => 'entity_reference',
        'label' => 'Market Activities Price premium unit',
        'description' => 'Market Activities Price premium unit',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'price_premium_unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_price_premium_unit_otr' => [
        'type' => 'string',
        'label' => 'Market Activities Other price premium unit',
        'description' => 'Market Activities Other price premium unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_price_premium_to_prod' => [
        'type' => 'fraction',
        'label' => 'Market Activities Price premium to producer',
        'description' => 'Market Activities Price premium to producer',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_product_diff_mthd' => [
        'type' => 'entity_reference',
        'label' => 'Market Activities Product differentiation method',
        'description' => 'Market Activities Product differentiation method',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'product_differentiation_method',
        'required' => TRUE,
        'multiple' => TRUE,
      ],
      'csc_m_act_product_diff_mthd_otr' => [
        'type' => 'string',
        'label' => 'Market Activities Other product differentiation method',
        'description' => 'Market Activities Other product differentiation method',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_mktng_mthd' => [
        'type' => 'entity_reference',
        'label' => 'Market Activities Marketing method',
        'description' => 'Market Activities Marketing method',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'marketing_method',
        'required' => TRUE,
        'multiple' => TRUE,
      ],
      'csc_m_act_mktng_mthd_otr' => [
        'type' => 'string',
        'label' => 'Market Activities Other marketing method',
        'description' => 'Market Activities Other marketing method',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_mktng_chnl_id' => [
        'type' => 'entity_reference',
        'label' => 'Market Activities Marketing channel identification',
        'description' => 'Market Activities Marketing channel identification',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'marketing_channel_identification',
        'required' => TRUE,
        'multiple' => TRUE,
      ],
      'csc_m_act_mktng_chnl_id_mthd_otr' => [
        'type' => 'string',
        'label' => 'Market Activities Other marketing channel identification method',
        'description' => 'Market Activities Other marketing channel identification method',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_m_act_traceability_mthd' => [
        'type' => 'entity_reference',
        'label' => 'Market Activities Traceability method',
        'description' => 'Market Activities Traceability method',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'traceability_method',
        'required' => TRUE,
        'multiple' => TRUE,
      ],
      'csc_m_act_traceability_mthd_otr' => [
        'type' => 'string',
        'label' => 'Market Activities Other traceability method',
        'description' => 'Market Activities Other traceability method',
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
