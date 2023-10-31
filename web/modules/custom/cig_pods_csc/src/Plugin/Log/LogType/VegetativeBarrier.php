<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Vegetative Barrier log type.
 *
 * @LogType(
 * id = "csc_vegetative_barrier",
 * label = @Translation("VegetativeBarrier"),
 * )
 */
class VegetativeBarrier extends FarmLogType {

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
        'csc_p601_species_category' => [
          'type' => 'list_string',
          'label' => 'Supplemental Data 601 Species Category',
          'description' => 'Supplemental Data 601 Species Category',
          'allowed_values' => [
            'Grasses' => t(string: 'Grasses'),
            'Grass forb mix' => t(string: 'Grass forb mix'),
            'Grass legume mix' => t(string: 'Grass legume mix'),
          ],
          'required' => FALSE,
          'multiple' => FALSE,
        ],
        'csc_p601_barrier_width' => [
          'type' => 'fraction',
          'label' => 'Supplemental Data Barrier Width (feet)',
          'description' => 'Supplemental Data Barrier Width (feet)',
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