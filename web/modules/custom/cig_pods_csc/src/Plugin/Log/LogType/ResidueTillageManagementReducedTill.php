<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Residue Tillage Management Reduced Till log type.
 *
 * @LogType(
 * id = "csc_residue_till_reduced_till",
 * label = @Translation("Residue Tillage Management Reduced Till Log"),
 * )
 */
class ResidueTillageManagementReducedTill extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p345_surface_disturbance' => [
        'type' => 'list_string',
        'label' => 'Supplemental Data 345 Surface Disturbance',
        'description' => 'Supplemental Data 345 Surface Disturbance',
		    'allowed_values' => [
          'None' => t(string: 'None'),
          'Seed row/ridge tillage for planting' => t(string: 'Seed row/ridge tillage for planting'),
          'Shallow across most of the soil surface' => t(string: 'Shallow across most of the soil surface'),
          'Vertical/mulch' => t(string: 'Vertical/mulch'),
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
