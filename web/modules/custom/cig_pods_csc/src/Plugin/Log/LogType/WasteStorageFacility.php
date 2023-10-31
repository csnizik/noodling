<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Waste Storage Facility log type.
 *
 * @LogType(
 * id = "csc_waste_storage_facility",
 * label = @Translation("WasteStorageFacility"),
 * )
 */
class WasteStorageFacility extends FarmLogType {

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
      'csc_p313_pri_waste_storage_sys' => [
        'type' => 'entity_reference',
        'label' => 'Supplemental Data Waste storage system prior to installing your waste storage facility',
        'description' => 'Supplemental Data Waste storage system prior to installing your waste storage facility',
          'target_type' => 'taxonomy_term',
          'target_bundle' => 'waste_storage_system',
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