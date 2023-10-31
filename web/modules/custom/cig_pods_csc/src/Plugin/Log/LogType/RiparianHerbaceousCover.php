<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Riparian Herbaceous Cover log type.
 *
 * @LogType(
 * id = "csc_riparian_herbaceous_cover",
 * label = @Translation("Riparian Herbaceous Cover Log"),
 * )
 */
class RiparianHerbaceousCover extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p390_species_category' => [
        'type' => 'list_string',
        'label' => 'Riparian Herbaceous Cover Species Category',
        'description' => 'Riparian Herbaceous Cover Species Category',
		    'allowed_values' => [
          'Ferns' => t(string: 'Ferns'),
          'Forbs' => t(string: 'Forbs'),
          'Grasses' => t(string: 'Grasses'),
          'Legumes' => t(string: 'Legumes'),
          'Rushes' => t(string: 'Rushes'),
          'Sedges' => t(string: 'Sedges'),
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
