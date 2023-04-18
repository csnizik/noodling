<?php

namespace Drupal\cig_pods_csc\Plugin\Asset\AssetType;

use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the CIG Producer asset type.
 *
 * @AssetType(
 * id = "producer_enrollment",
 * label = @Translation("ProducerEnrollment"),
 * )
 */
class ProducerEnrollment extends FarmAssetType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'project_id' => [
        'type' => 'entity_reference',
        'label' => 'Project ID',
        'description' => 'Project ID',
		    'target_type' => 'asset',
		    'target_bundle' => 'project_summary',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'p_enrollment_farm_id' => [
        'type' => 'string',
        'label' => 'Farm ID',
        'description' => 'Farm ID',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'p_enrollment_state' => [
        'type' => 'entity_reference',
        'label' => 'State or Territory',
        'description' => 'State or Territory',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'state',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'p_enrollment_county' => [
        'type' => 'entity_reference',
        'label' => 'County',
        'description' => 'County',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'county',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'p_enrollment_start_date' => [
        'type' => 'timestamp',
        'label' => 'Producer Start Date',
        'description' => 'Producer Start Date',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'p_enrollment_underserved_status' => [
        'type' => 'list_string',
        'label' => 'Underserved Status',
        'description' => 'Underserved Status',
        'allowed_values' => [
          'Yes, underserved' => t(string: 'Yes, underserved'),
          'Yes, small producer' => t(string: 'Yes, small producer'),
          'Yes, underserved and small producer' => t(string: 'Yes, underserved and small producer'),
          'Program income' => t(string: 'Program income'),
          'No' => t(string: 'No'),
          "I don't know" => t(string: "I don't know"),
        ],
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'p_enrollment_total_area' => [
        'type' => 'entity_reference',
        'label' => 'Total Area',
        'description' => 'Total Area',
		    'target_type' => 'taxonomy_term',
        'target_bundle' => 'total_area',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'p_enrollment_total_crop_area' => [
        'type' => 'fraction',
        'label' => 'Total Crop Area',
        'description' => 'Total Crop Area',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'p_enrollment_total_livestock_area' => [
        'type' => 'fraction',
        'label' => 'Total Livestock Area',
        'description' => 'Total Livestock Area',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'p_enrollment_total_forest_area' => [
        'type' => 'fraction',
        'label' => 'Total Forest Area',
        'description' => 'Total Forest Area',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'p_enrollment_livestock_type_1' => [
        'type' => 'entity_reference',
        'label' => 'Livestock Type 1',
        'description' => 'Livestock Type 1',
		    'target_type' => 'taxonomy_term',
        'target_bundle' => 'livestock_type',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'p_enrollment_livestock_type_1_count' => [
        'type' => 'fraction',
        'label' => 'Livestock head (type 1 avg annual)',
        'description' => 'Livestock head (type 1 avg annual)',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'p_enrollment_livestock_type_2' => [
        'type' => 'entity_reference',
        'label' => 'Livestock Type 2',
        'description' => 'Livestock Type 2',
		    'target_type' => 'taxonomy_term',
        'target_bundle' => 'livestock_type',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'p_enrollment_livestock_type_2_count' => [
        'type' => 'fraction',
        'label' => 'Livestock head (type 2 avg annual)',
        'description' => 'Livestock head (type 2 avg annual)',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'p_enrollment_livestock_type_3' => [
        'type' => 'entity_reference',
        'label' => 'Livestock Type 3',
        'description' => 'Livestock Type 3',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'livestock_type',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'p_enrollment_livestock_type_3_count' => [
        'type' => 'fraction',
        'label' => 'Livestock head (type 3 avg annual)',
        'description' => 'Livestock head (type 3 avg annual)',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_livestock_type_other' => [
        'type' => 'string',
        'label' => 'Other livestock type',
        'description' => 'Other livestock type',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_organic_farm' => [
        'type' => 'entity_reference',
        'label' => 'Organic Farm',
        'description' => 'Organic Farm',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'organic_farm',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_organic_fields' => [
        'type' => 'entity_reference',
        'label' => 'Organic Fields',
        'description' => 'Organic Fields',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'organic_fields',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_producer_motivation' => [
        'type' => 'list_string',
        'label' => 'Producer Motivation',
        'description' => 'Producer Motivation',
        'allowed_values' => [
          'Environment benefit' => t(string: 'Environment benefit'),
          'Financial benefit' => t(string: 'Financial benefit'),
          'New market opportunity' => t(string: 'New market opportunity'),
          'Partnerships or networks' => t(string: 'Partnerships or networks'),
          'Other' => t(string: 'Other'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_producer_outreach' => [
        'type' => 'entity_reference',
        'label' => 'Producer Outreach',
        'description' => 'Producer Outreach',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'producer_outreach',
        'required' => TRUE,
        'multiple' => TRUE,
      ],
	    'p_enrollment_producer_outreach_other' => [
        'type' => 'string',
        'label' => 'Other Producer Outreach',
        'description' => 'Other Producer Outreach',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_csaf_experience' => [
        'type' => 'entity_reference',
        'label' => 'CASF Experience',
        'description' => 'CSAF Experience',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'csaf_experience',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_csaf_federal_funds' => [
        'type' => 'entity_reference',
        'label' => 'CASF Federal Funds',
        'description' => 'CSAF Federal Funds',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'csaf_federal_funds',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_csaf_state_local_funds' => [
        'type' => 'entity_reference',
        'label' => 'CASF State Local Funds',
        'description' => 'CSAF State Local Funds',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'csaf_state_or_local_funds',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_csaf_nonprofit_funds' => [
        'type' => 'entity_reference',
        'label' => 'CASF nonprofit Funds',
        'description' => 'CASF nonprofit Funds',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'csaf_nonprofit_funds',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'p_enrollment_csaf_market_incentives' => [
        'type' => 'entity_reference',
        'label' => 'CASF market incentives',
        'description' => 'CASF market incentives',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'csaf_market_incentives',
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
