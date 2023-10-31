<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Waste Separation Facility log type.
 *
 * @LogType(
 * id = "csc_waste_separation_facility",
 * label = @Translation("WasteSeparationFacility"),
 * )
 */
class WasteSeparationFacility extends FarmLogType {

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
      'csc_p632_separation_type' => [
        'type' => 'list_string',
        'label' => 'Supplemental Data Separation type',
        'description' => 'Supplemental Data Separation type',
            'allowed_values' => [
          'Chemical (e.g., salts, polymers)' => t(string: 'Chemical (e.g., salts, polymers)'),
          'Mechanical (e.g., screens, presses)' => t(string: 'Mechanical (e.g., screens, presses)'),
          'Settling basin' => t(string: 'Settling basin'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p632_use_of_solids' => [
        'type' => 'list_string',
        'label' => 'Supplemental Data Most common use of solids',
        'description' => 'Supplemental Data Most common use of solids',
            'allowed_values' => [
          'Bedding' => t(string: 'Bedding'),
          'Field applied' => t(string: 'Field applied'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p632_use_of_solids_other' => [
        'type' => 'string',
        'label' => 'Supplemental Data Other most common use of solids',
        'description' => 'Supplemental Data Other most common use of solids',
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