<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Tree Shrub Establishment log type.
 *
 * @LogType(
 * id = "csc_tree_shrub_establishment",
 * label = @Translation("Tree Shrub Establishment Log"),
 * )
 */
class TreeShrubEstablishment extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p612_species_category' => [
        'type' => 'list_string',
        'label' => 'Tree Shrub Establishment 612 Species Category',
        'description' => 'Tree Shrub Establishment 612 Species Category',
		    'allowed_values' => [
          'Coniferous trees' => t(string: 'Coniferous trees'),
          'Deciduous trees' => t(string: 'Deciduous trees'),
          'Shrubs' => t(string: 'Shrubs'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p612_species_density' => [
        'type' => 'fraction',
        'label' => 'Tree Shrub Establishment 612 Species density',
        'description' => 'Tree Shrub Establishment 612 Species density',
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
