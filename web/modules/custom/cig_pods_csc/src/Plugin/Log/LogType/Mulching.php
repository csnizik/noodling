<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Mulching log type.
 *
 * @LogType(
 * id = "mulching",
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
      'project_id' => [
        'type' => 'entity_reference',
        'label' => 'Project ID',
        'description' => 'Project ID',
        'target_type' => 'asset',
        'target_bundle' => 'project_summary',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'p484_mulch_coverage' => [
        'type' => 'fraction',
        'label' => 'Mulching Mulch cover',
        'description' => 'Mulching Mulch cover',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'p590_nutrient_type' => [
        'type' => 'entity_reference',
        'label' => 'Mulching Nutrient type with CPS 590',
        'description' => 'Mulching Nutrient type with CPS 590',
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'nutrient_type',
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
