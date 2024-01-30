<?php

namespace Drupal\cig_pods_csc\Plugin\Asset\AssetType;

use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Import Attempt History asset type.
 *
 * @AssetType(
 * id = "csc_import_history",
 * label = @Translation("ImportHistory"),
 * )
 */
class ImportHistory extends FarmAssetType {

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
        'target_bundle' => 'csc_project_summary',
        'required' => TRUE,
        'multiple' => FALSE,
      ],

      /*
       * NOTE: use of allowed_values is being phased out in favor of taxonomies
       * This change will be considered for this field as well.
       */
      'csc_attempt_status' => [
        'type' => 'list_string',
        'label' => 'Attempt Status',
        'description' => 'Attempt Status',
        'allowed_values' => [
          'Failed' => t(string: 'Failed'),
          'Success' => t(string: 'Success'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],

      /*
       * NOTE: use of allowed_values is being phased out in favor of taxonomies
       * This change will be considered for this field as well.
       */
      'csc_workbook_type' => [
        'type' => 'list_string',
        'label' => 'Workbook Type',
        'description' => 'Workbook Type',
        'allowed_values' => [
          'Main' => t(string: 'Main'),
          'Supplemental' => t(string: 'Supplemental'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_year_of_reporting' => [
        'type' => 'fraction',
        'label' => 'Year of Reporting',
        'description' => 'Year of Reporting',
        'required' => TRUE,
        'multiple' => FALSE,
      ],

      /*
       * NOTE: use of allowed_values is being phased out in favor of taxonomies
       * This change will be considered for this field as well.
       */
      'csc_month_of_reporting' => [
        'type' => 'list_string',
        'label' => 'Month of Reporting',
        'description' => 'Month of Reporting',
        'allowed_values' => [
          'Jan-Mar' => t(string: 'January 1 - March 31'),
          'Apr-Jun' => t(string: 'April 1 - June 30'),
          'Jul-Sep' => t(string: 'July 1 - September 30'),
          'Oct-Dec' => t(string: 'October 1 - December 31'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_file_uploaded' => [
        'type' => 'string',
        'label' => 'File Uploaded',
        'description' => 'File Uploaded',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_time_submitted' => [
        'type' => 'timestamp',
        'label' => 'Time Submitted',
        'description' => 'Time Submitted',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_by_user' => [
        'type' => 'string',
        'label' => 'By User',
        'description' => 'By User',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_submission_details' => [
        'type' => 'string',
        'label' => 'Submission Details',
        'description' => 'Submission Details',
        'required' => FALSE,
        'multiple' => FALSE,
      ],
      'csc_import_summary_worksheets' => [
        'type' => 'string',
        'label' => 'Import Details for all worksheets',
        'description' => 'Import Details for all worksheets',
        'required' => FALSE,
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
