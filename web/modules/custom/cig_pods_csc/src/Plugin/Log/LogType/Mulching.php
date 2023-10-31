<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Mulching log type.
 *
 * @LogType(
 * id = "csc_mulching",
 * label = @Translation("Mulching Log"),
 * )
 */
class Mulching extends FarmLogType {

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
      'csc_p484_mulch_coverage' => [
        'type' => 'fraction',
        'label' => 'Mulching, Mulch cover',
        'description' => 'Mulching, Mulch cover',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p484_mulch_type' => [
        'type' => 'list_string',
        'label' => 'Mulching, Mulch Type',
        'description' => 'Mulching, Mulch Type',
		    'allowed_values' => [
          'Gravel' => t(string: 'Gravel'),
          'Natural' => t(string: 'Natural'),
          'Synthetic' => t(string: 'Synthetic'),
          'Wood' => t(string: 'Wood'),
        ],
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
