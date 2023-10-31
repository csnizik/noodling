<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Prescribed Grazing log type.
 *
 * @LogType(
 * id = "csc_prescribed_grazing",
 * label = @Translation("Prescribed Grazing Log"),
 * )
 */
class PrescribedGrazing extends FarmLogType {

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
      'csc_p528_grazing_type' => [
        'type' => 'list_string',
        'label' => 'Prescribed Grazing Grazing type',
        'description' => 'Supplemental Data Grazing type',
		    'allowed_values' => [
          'Cell grazing' => t(string: 'Cell grazing'),
          'Deferred rotational' => t(string: 'Deferred rotation'),
          'Management intensive' => t(string: 'Warm-season broadleaf'),
          'Rest-rotation' => t(string: 'Rest-rotation'),
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
