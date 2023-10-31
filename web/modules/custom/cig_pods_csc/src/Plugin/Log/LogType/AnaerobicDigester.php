<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Anaerobic Digester log type.
 *
 * @LogType(
 * id = "csc_anaerobic_digester",
 * label = @Translation("Anaerobic Digester Log"),
 * )
 */
class AnaerobicDigester extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p366_prior_waste_storage_sys' => [
        'type' => 'entity_reference',
        'label' => 'Anaerobic Digester Waste storage system prior to installing',
        'description' => 'Anaerobic Digester Waste storage system prior to installing',
		    'target_type' => 'taxonomy_term',
		    'target_bundle' => 'waste_storage_system',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p366_digester_type' => [
        'type' => 'list_string',
        'label' => 'Anaerobic Digester Digester type',
        'description' => 'Anaerobic Digester Digester type',
		    'allowed_values' => [
          'Covered lagoon with energy generation' => t(string: 'Covered lagoon with energy generation'),
          'Covered lagoon with flaring' => t(string: 'Covered lagoon with flaring'),
          'Covered lagoon (no energy generation or flaring)' => t(string: 'Covered lagoon (no energy generation or flaring)'),
          'Complex mix with energy generation' => t(string: 'Complex mix with energy generation'),
          'Plug flow with energy generation' => t(string: 'Plug flow with energy generation'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p366_digester_type_other' => [
        'type' => 'string',
        'label' => 'Anaerobic Digester Other digester type',
        'description' => 'Anaerobic Digester Other digester type',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p366_addtl_fdbk_source_otr' => [
        'type' => 'list_string',
        'label' => 'Anaerobic Digester Additional feedstock source',
        'description' => 'Anaerobic Digester Additional feedstock source',
		    'allowed_values' => [
          'Food waste' => t(string: 'Food waste'),
          'Straw or bedding' => t(string: 'Straw or bedding'),
          'Wastewater' => t(string: 'Wastewater'),
          'Other(Specify)' => t(string: 'Other(Specify)'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p366_addtl_feedback_source_other' => [
        'type' => 'string',
        'label' => 'Anaerobic Digester Other additional feedstock source',
        'description' => 'Anaerobic Digester Other additional feedstock source',
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
