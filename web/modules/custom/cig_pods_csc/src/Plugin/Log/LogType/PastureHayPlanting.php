<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Pasture and Hay Planting log type.
 *
 * @LogType(
 * id = "csc_pasture_hay_planting",
 * label = @Translation("Pasture and Hay Planting Log"),
 * )
 */
class PastureHayPlanting extends FarmLogType {

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
      'csc_p512_species_category' => [
        'type' => 'list_string',
        'label' => 'Pasture and Hay Planting Species Category',
        'description' => 'Supplemental Data Species Category',
		    'allowed_values' => [
          'Cool-season broadleaf' => t(string: 'Cool-season broadleaf'),
          'Cool-season grass' => t(string: 'Cool-season grass'),
          'Warm-season broadleaf' => t(string: 'Warm-season broadleaf'),
          'Warm-season grass' => t(string: 'Warm-season grass'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p512_termination_process' => [
        'type' => 'list_string',
        'label' => 'Pasture and Hay Planting Termination process',
        'description' => 'Supplemental Data Termination process',
		    'allowed_values' => [
          'Grazing' => t(string: 'Grazing'),
          'Haying' => t(string: 'Haying'),
          'Other(Specify)' => t(string: 'Other(Specify)'),
        ],
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_p512_otr_term_process' => [
        'type' => 'string',
        'label' => 'Pasture and Hay Planting Other termination process',
        'description' => 'Supplemental Data Other termination process',
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
