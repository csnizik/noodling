<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Import Details log type.
 *
 * @LogType(
 * id = "csc_import_details",
 * label = @Translation("Import Details"),
 * )
 */
class ImportDetails extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_import_sheetname' => [
        'type' => 'string',
        'label' => 'Import Details Sheet Name',
        'description' => 'Import Details Sheet Name',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_import_record_cnt' => [
        'type' => 'fraction',
        'label' => 'Import Details Record Count',
        'description' => 'Import Details Record Count',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_import_updated_cnt' => [
        'type' => 'fraction',
        'label' => 'Import Details Updated Records Count',
        'description' => 'Import Details Updated Records Count',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_import_entity_type' => [
        'type' => 'string',
        'label' => 'Import Details Entity Type (asset or log)',
        'description' => 'Import Details Entity Type (asset or log)',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_import_machine_name' => [
        'type' => 'string',
        'label' => 'Import Details Entity Machine Name',
        'description' => 'Import Details Entity Machine Name',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_import_records_before' => [
        'type' => 'fraction',
        'label' => 'Import Details Number of Records Before Import',
        'description' => 'Import Details Number of Records Before Import',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_import_history_reference' => [
          'type' => 'entity_reference',
          'label' => 'Import History Reference',
          'description' => 'Relate this entity to its respective import',
          'target_type' => 'asset',
          'multiple' => TRUE,
          'cardinality' => -1,
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
