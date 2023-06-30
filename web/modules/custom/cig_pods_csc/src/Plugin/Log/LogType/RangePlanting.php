<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Range Planting log type.
 *
 * @LogType(
 * id = "range_planting",
 * label = @Translation("Range Planting Log"),
 * )
 */
class RangePlanting extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
        'p550_species_category' => [
            'type' => 'list_string',
            'label' => 'Supplemental Data 550 Species category',
            'description' => 'Supplemental Data 550 Species category',
                'allowed_values' => [
              'Forbs' => t(string: 'Forbs'),
              'Grasses' => t(string: 'Grasses'),
              'Legumes' => t(string: 'Legumes'),
              'Shrubs' => t(string: 'Shrubs'),
              'Trees' => t(string: 'Trees'),
            ],
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