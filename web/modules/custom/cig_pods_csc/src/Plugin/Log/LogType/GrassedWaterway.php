<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Grassed Waterway log type.
 *
 * @LogType(
 * id = "grassed_waterway",
 * label = @Translation("Grassed Waterway"),
 * )
 */
class GrassedWaterway extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'p412_species_category' => [
        'type' => 'list_string',
        'label' => 'Supplemental Data 412 Species category',
        'description' => 'Supplemental Data 412 Species category',
		    'allowed_values' => [
          'Flowering plants,' => t(string: 'Flowering plants'),
          'Forbs' => t(string: 'Forbs'),
          'Grasses' => t(string: 'Grasses'),
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