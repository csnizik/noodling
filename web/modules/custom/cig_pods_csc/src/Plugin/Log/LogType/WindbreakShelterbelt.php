<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Windbreak Shelterbelt log type.
 *
 * @LogType(
 * id = "csc_windbreak_shelterbelt",
 * label = @Translation("Windbreak Shelterbelt"),
 * )
 */
class WindbreakShelterbelt extends FarmLogType {

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
      'csc_p380_species_category' => [
        'type' => 'list_string',
        'label' => 'Windbreak Shelterbelt 380 Species category',
        'description' => 'Windbreak Shelterbelt 380 Species category',
		    'allowed_values' => [
          'Coniferous trees' => t(string: 'Coniferous trees'),
          'Deciduous trees' => t(string: 'Deciduous trees'),
          'Shrubs' => t(string: 'Shrubs'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p380_species_density' => [
        'type' => 'fraction',
        'label' => 'Windbreak Shelterbelt 380 Species Density',
        'description' => 'Windbreak Shelterbelt 380 Species Density',
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