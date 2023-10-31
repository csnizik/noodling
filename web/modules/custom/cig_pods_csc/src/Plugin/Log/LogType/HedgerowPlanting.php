<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Hedgerow Planting log type.
 *
 * @LogType(
 * id = "csc_hedgerow_planting",
 * label = @Translation("Hedgerow Planting"),
 * )
 */
class HedgerowPlanting extends FarmLogType {

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
      'csc_p422_species_category' => [
        'type' => 'list_string',
        'label' => 'Supplemental Data 422 Species category',
        'description' => 'Supplemental Data 422 Species category',
		    'allowed_values' => [
          'Grasses' => t(string: 'Grasses'),
          'Shrubs' => t(string: 'Shrubs'),
          'Trees' => t(string: 'Trees'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p422_species_density' => [
        'type' => 'fraction',
        'label' => 'Supplemental Data 422 Species density',
        'description' => 'Supplemental Data 422 Species density',
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