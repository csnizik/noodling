<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Forest Farming log type.
 *
 * @LogType(
 * id = "csc_forest_farming",
 * label = @Translation("Forest Farming"),
 * )
 */
class ForestFarming extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p379_land_use_prev_years' => [
        'type' => 'list_string',
        'label' => 'Forest Farming Land use in previous years',
        'description' => 'Forest Farming Land use in previous years',
		    'allowed_values' => [
          'Forest' => t(string: 'Forest'),
          'Multi-story cropping' => t(string: 'Multi-story cropping'),
          'Row crops' => t(string: 'Row crops'),
          'Pasture/grazing land' => t(string: 'Pasture/grazing land'),
          'Other agroforestry' => t(string: 'Other agroforestry '),
        ],
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
