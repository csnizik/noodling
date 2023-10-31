<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Roofs And Covers log type.
 *
 * @LogType(
 * id = "csc_roofs_and_covers",
 * label = @Translation("Roofs And Covers Log"),
 * )
 */
class RoofsAndCovers extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p367_roof_cover_type' => [
        'type' => 'list_string',
        'label' => 'Roofs And Covers Roof/cover type',
        'description' => 'Roofs And Covers Roof/cover type',
		    'allowed_values' => [
          'Concrete' => t(string: 'Concrete'),
          'Flexible geomembrane' => t(string: 'Flexible geomembrane'),
          'Grasses' => t(string: 'Grasses'),
          'Metal' => t(string: 'Metal'),
          'Timber' => t(string: 'Timber'),
          'Other (specify)' => t(string: 'Other (specify)'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p367_roof_cover_type_other' => [
        'type' => 'string',
        'label' => 'Roofs And Covers Other roof/cover type',
        'description' => 'Roofs And Covers Other roof/cover type',
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
