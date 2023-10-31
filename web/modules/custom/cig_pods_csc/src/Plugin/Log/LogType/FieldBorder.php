<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Field Border log type.
 *
 * @LogType(
 * id = "csc_field_border",
 * label = @Translation("Field Border"),
 * )
 */
class FieldBorder extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p386_species_category' => [
        'type' => 'list_string',
        'label' => 'Field Border 386 Species category',
        'description' => 'Field Border 386 Species category',
		    'allowed_values' => [
          'Forbs' => t(string: 'Forbs'),
          'Grasses' => t(string: 'Grasses'),
          'Mix' => t(string: 'Mix'),
          'Shrubs' => t(string: 'Shrubs'),
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
