<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Waste Treatment Lagoon log type.
 *
 * @LogType(
 * id = "waste_treatment_lagoon",
 * label = @Translation("Waste Treatment Lagoon"),
 * )
 */
class WasteTreatmentLagoon extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'project_id' => [
        'type' => 'entity_reference',
        'label' => 'Project ID',
        'description' => 'Project ID',
        'target_type' => 'asset',
        'target_bundle' => 'project_summary',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'field_id' => [
        'type' => 'entity_reference',
        'label' => 'Field ID',
        'description' => 'Field ID',
		    'target_type' => 'asset',
		    'target_bundle' => 'field_enrollment',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'p359_prior_waste_storage_system' => [
        'type' => 'entity_reference',
        'label' => 'Waste Treatment Lagoon Waste storage system prior to installing waste treatment lagoon',
        'description' => 'Waste Treatment Lagoon Waste storage system prior to installing waste treatment lagoon',
		    'target_type' => 'taxonomy_term',
		    'target_bundle' => 'waste_storage_system',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'p359_lagoon_cover_or_crust' => [
        'type' => 'boolean',
        'label' => 'Waste Treatment Lagoon Is there a lagoon cover/crust?',
        'description' => 'Waste Treatment Lagoon Is there a lagoon cover/crust?',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'p359_lagoon_aeration' => [
        'type' => 'boolean',
        'label' => 'Waste Treatment Lagoon Is there lagoon aeration?',
        'description' => 'Waste Treatment Lagoon Is there lagoon aeration?',
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