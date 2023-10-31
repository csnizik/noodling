<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Nutrient Management log type.
 *
 * @LogType(
 * id = "csc_nutrient_management",
 * label = @Translation("Nutrient Management Log"),
 * )
 */
class NutrientManagement extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
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
      'csc_p590_nutrient_type' => [
        'type' => 'entity_reference',
        'label' => 'Nutrient Management Nutrient type with CPS 590',
        'description' => 'Supplemental Data Nutrient type with CPS 590',
		    'target_type' => 'taxonomy_term',
		    'target_bundle' => 'nutrient_type',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p590_application_method' => [
        'type' => 'list_string',
        'label' => 'Nutrient Management Nutrient application method with CPS 590',
        'description' => 'Supplemental Data Nutrient application method with CPS 590',
		    'allowed_values' => [
          'Banded' => t(string: 'Banded'),
          'Broadcast' => t(string: 'Broadcast'),
          'Injection' => t(string: 'Injection'),
          'Irrigation' => t(string: 'Irrigation'),
          'Surface application' => t(string: 'Surface application'),
          'Surface application with tillage' => t(string: 'Surface application with tillage'),
          'Variable rate' => t(string: 'Variable rate'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p590_pri_aplctn_method' => [
        'type' => 'list_string',
        'label' => 'Nutrient Management Nutrient application method in the previous year',
        'description' => 'Supplemental Data Nutrient application method in the previous year',
		    'allowed_values' => [
          'Banded' => t(string: 'Banded'),
          'Broadcast' => t(string: 'Broadcast'),
          'Injection' => t(string: 'Injection'),
          'Irrigation' => t(string: 'Irrigation'),
          'Surface application' => t(string: 'Surface application'),
          'Surface application with tillage' => t(string: 'Surface application with tillage'),
          'Variable rate' => t(string: 'Variable rate'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p590_application_timing' => [
        'type' => 'list_string',
        'label' => 'Nutrient Management Nutrient application timing with CPS 590',
        'description' => 'Supplemental Data Nutrient application timing with CPS 590',
		    'allowed_values' => [
          'Single pre-planting' => t(string: 'Single pre-planting'),
          'Broadcast' => t(string: 'Broadcast'),
          'Single post-planting' => t(string: 'Single post-planting'),
          'Split pre- and post-planting' => t(string: 'Split pre- and post-planting'),
          'Split post-planting' => t(string: 'Split post-planting'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p590_pri_aplctn_timing' => [
        'type' => 'list_string',
        'label' => 'Nutrient Management Nutrient application timing in the previous year',
        'description' => 'Supplemental Data Nutrient application timing in the previous year',
		    'allowed_values' => [
          'Single pre-planting' => t(string: 'Single pre-planting'),
          'Broadcast' => t(string: 'Broadcast'),
          'Single post-planting' => t(string: 'Single post-planting'),
          'Split pre- and post-planting' => t(string: 'Split pre- and post-planting'),
          'Split post-planting' => t(string: 'Split post-planting'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p590_application_rate' => [
        'type' => 'fraction',
        'label' => 'Nutrient Management Nutrient application rate with CPS 590',
        'description' => 'Supplemental Data Nutrient application rate with CPS 590',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p590_application_rate_unit' => [
        'type' => 'list_string',
        'label' => 'Nutrient Management Nutrient application rate unit with CPS 590',
        'description' => 'Supplemental Data Nutrient application rate unit with CPS 590',
		    'allowed_values' => [
          'Gallons per acre' => t(string: 'Gallons per acre'),
          'Pounds per acre' => t(string: 'Pounds per acre'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p590_aplctn_rate_change' => [
        'type' => 'list_string',
        'label' => 'Nutrient Management Nutrient application rate change',
        'description' => 'Supplemental Data Nutrient application rate change',
		    'allowed_values' => [
          'Decrease compared to previous year' => t(string: 'Decrease compared to previous year'),
          'Increase compare to previous year, no change' => t(string: 'Increase compare to previous year, no change'),
        ],
        'required' => FALSE,
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
