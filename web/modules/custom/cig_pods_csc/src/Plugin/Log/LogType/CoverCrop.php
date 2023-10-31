<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Cover Crop log type.
 *
 * @LogType(
 * id = "csc_cover_crop",
 * label = @Translation("CoverCrop"),
 * )
 */
class CoverCrop extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
        'csc_p340_species_category' => [
            'type' => 'list_string',
            'label' => 'Supplemental Data 340 Species category',
            'description' => 'Supplemental Data 340 Species category',
                'allowed_values' => [
              'Brassicas' => t(string: 'Brassicas'),
              'Forbs' => t(string: 'Forbs'),
              'Grasses' => t(string: 'Grasses'),
              'Legume' => t(string: 'Legume'),
              'Non-legume broadleaves' => t(string: 'Non-legume broadleaves'),
            ],
            'required' => FALSE,
            'multiple' => FALSE,
        ],
        'csc_p340_planned_management' => [
            'type' => 'list_string',
            'label' => 'Supplemental Data Cover crop planned management',
            'description' => 'Supplemental Data Cover crop planned management',
                'allowed_values' => [
              'Grazing' => t(string: 'Grazing'),
              'Haying' => t(string: 'Haying'),
              'Termination' => t(string: 'Termination'),
            ],
            'required' => FALSE,
            'multiple' => FALSE,
        ],
        'csc_p340_termination_method' => [
            'type' => 'list_string',
            'label' => 'Supplemental Data Cover crop termination method',
            'description' => 'Supplemental Data Cover crop termination method',
                'allowed_values' => [
              'Burning' => t(string: 'Burning'),
              'Herbicide application' => t(string: 'Herbicide application'),
              'Incorporation, mowing' => t(string: 'Incorporation, mowing'),
              'Rolling/crimping' => t(string: 'Rolling/crimping'),
              'Winter kill/frost' => t(string: 'Winter kill/frost'),
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
