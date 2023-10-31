<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Critical Area Planting log type.
 *
 * @LogType(
 * id = "csc_critical_area_planting",
 * label = @Translation("CriticalAreaPlanting"),
 * )
 */
class CriticalAreaPlanting extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
        'csc_p342_species_category' => [
            'type' => 'list_string',
            'label' => 'Supplemental Data 342 Species category',
            'description' => 'Supplemental Data 342 Species category',
                'allowed_values' => [
              'Grass' => t(string: 'Grass'),
              'Grass legume/forb mix' => t(string: 'Grass legume/forb mix'),
              'Herbaceous woody mix' => t(string: 'Herbaceous woody mix'),
              'Perennial or reseeding' => t(string: 'Perennial or reseeding'),
              'Shrubs' => t(string: 'Shrubs'),
              'Trees' => t(string: 'Trees'),
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
