<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Stripcropping log type.
 *
 * @LogType(
 * id = "stripcropping",
 * label = @Translation("Stripcropping Log"),
 * )
 */
class Stripcropping extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'p585_strip_width' => [
        'type' => 'fraction',
        'label' => 'Stripcropping Strip width (Feet)',
        'description' => 'Stripcropping Strip width (Feet)',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'p585_crop_category' => [
        'type' => 'list_string',
        'label' => 'Stripcropping Crop category',
        'description' => 'Stripcropping Crop category',
		    'allowed_values' => [
          'Erosion resistant crops' => t(string: 'Erosion resistant crops'),
          'Sediment trapping crops' => t(string: 'Sediment trapping crops'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'p585_number_of_strips' => [
        'type' => 'fraction',
        'label' => 'Stripcropping number of strips',
        'description' => 'Stripcropping number of strips',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'project_id' => [
        'type' => 'entity_reference',
        'label' => 'Project ID',
        'description' => 'Project ID',
        'target_type' => 'asset',
        'target_bundle' => 'project_summary',
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
