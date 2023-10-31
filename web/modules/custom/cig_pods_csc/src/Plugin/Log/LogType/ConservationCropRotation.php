<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Conservation Crop Rotation log type.
 *
 * @LogType(
 * id = "csc_conservation_crop_rotation",
 * label = @Translation("ConservationCropRotation"),
 * )
 */
class ConservationCropRotation extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
        'csc_p328_conservation_crop_type' => [
            'type' => 'list_string',
            'label' => 'Supplemental Data Conservation Crop Type',
            'description' => 'Supplemental Data Conservation Crop Type',
                'allowed_values' => [
              'Brassicas' => t(string: 'Brassicas'),
              'Broadleaf' => t(string: 'Broadleaf'),
              'Cool season' => t(string: 'Cool season'),
              'Grass, legume' => t(string: 'Grass, legume'),
              'Warm season' => t(string: 'Warm season'),
            ],
            'required' => FALSE,
            'multiple' => FALSE,
        ],
        'csc_p328_change_implemented' => [
            'type' => 'list_string',
            'label' => 'Supplemental Data Change implemented',
            'description' => 'Supplemental Data Change implemented',
                'allowed_values' => [
              'Added perennial crop' => t(string: 'Added perennial crop'),
              'Broadleaf' => t(string: 'Broadleaf'),
              'Reduced fallow period' => t(string: 'Reduced fallow period'),
              'Both' => t(string: 'Both'),
            ],
            'required' => FALSE,
            'multiple' => FALSE,
        ],
        'csc_p328_rotation_tillage_type' => [
            'type' => 'list_string',
            'label' => 'Supplemental Data Conservation crop rotation tillage type',
            'description' => 'Supplemental Data Conservation crop rotation tillage type',
                'allowed_values' => [
              'Conventional (plow, chisel, disk)' => t(string: 'Conventional (plow, chisel, disk)'),
              'No-till, direct seed' => t(string: 'No-till, direct seed'),
              'Reduced till' => t(string: 'Reduced till'),
              'Strip till' => t(string: 'Strip till'),
              'None' => t(string: 'None'),
              'other (specify)' => t(string: 'other (specify)'),
            ],
            'required' => FALSE,
            'multiple' => FALSE,
        ],
        'csc_p328_rotation_till_type_otr' => [
            'type' => 'string',
            'label' => 'Supplemental Data Other conservation crop rotation tillage type',
            'description' => 'Supplemental Data Other conservation crop rotation tillage type',
            'required' => FALSE,
            'multiple' => FALSE,
        ],
        'csc_p328_total_rotation_length' => [
            'type' => 'fraction',
            'label' => 'Supplemental Data Total conservation crop rotation length in days',
            'description' => 'Supplemental Data Total conservation crop rotation length in days',
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
