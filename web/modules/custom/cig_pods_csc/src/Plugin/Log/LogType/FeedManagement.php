<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Field Management log type.
 *
 * @LogType(
 * id = "csc_feed_management",
 * label = @Translation("Feed Management"),
 * )
 */
class FeedManagement extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_p592_crude_protein_percent' => [
        'type' => 'fraction',
        'label' => 'Feed Management Crude protein (Percent)',
        'description' => 'Feed Management Crude protein (Percent)',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p592_fat_percent' => [
        'type' => 'fraction',
        'label' => 'Feed Management Fat (percent)',
        'description' => 'Feed Management Fat (percent)',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p592_feed_additives' => [
        'type' => 'list_string',
        'label' => 'Feed Management Feed additives/supplements',
        'description' => 'Feed Management Feed additives/supplements',
		    'allowed_values' => [
          'Chemical' => t(string: 'Chemical'),
          'Edible oils/fats' => t(string: 'Edible oils/fats'),
          'Seaweed/kelp' => t(string: 'Seaweed/kelp'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p592_feed_additives_other' => [
        'type' => 'string',
        'label' => 'Feed Management Other Feed additives/supplements',
        'description' => 'Feed Management Other feed additives/supplements',
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
