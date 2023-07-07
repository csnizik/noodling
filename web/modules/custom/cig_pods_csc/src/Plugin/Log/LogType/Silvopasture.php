<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Silvopasture log type.
 *
 * @LogType(
 * id = "silvopasture",
 * label = @Translation("Silvopasture Log"),
 * )
 */
class Silvopasture extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'p381_species_category' => [
        'type' => 'list_string',
        'label' => 'Silvopasture 381 Species category',
        'description' => 'Silvopasture 381 Species category',
		    'allowed_values' => [
          'Coniferous trees' => t(string: 'Coniferous trees'),
          'Deciduous trees' => t(string: 'Deciduous trees'),
          'Forage' => t(string: 'Forage'),
          'Shrubs' => t(string: 'Shrubs'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'p381_species_density' => [
        'type' => 'fraction',
        'label' => 'Silvopasture 381 Species density',
        'description' => 'Silvopasture 381 Species density',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
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
