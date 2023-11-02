<?php

namespace Drupal\cig_pods_csc\Plugin\Asset\AssetType;

use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the CIG csc_project asset type.
 *
 * @AssetType(
 * id = "csc_project",
 * label = @Translation("Project"),
 * )
 */
class Project extends FarmAssetType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_project_id_field' => [
        'type' => 'string',
        'label' => 'Project ID',
        'description' => 'Project ID',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_project_grantee_org' => [
        'type' => 'string',
        'label' => 'Grantee Organization Name',
        'description' => 'Grantee Organization Name',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_project_grantee_cont_name' => [
        'type' => 'string',
        'label' => 'Grantee Primary Point of Contact',
        'description' => 'Grantee Primary Point of Contact',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_project_grantee_cont_email' => [
        'type' => 'string',
        'label' => 'Grantee Primary Point of Contact Email',
        'description' => 'Grantee Primary Point of Contact Email',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_project_start' => [
        'type' => 'timestamp',
        'label' => 'Overall Project Start Date',
        'description' => 'Overall Project Start Date',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_project_end' => [
        'type' => 'timestamp',
        'label' => 'Overall Project End Date',
        'description' => 'Overall Project End Date',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_project_budget' => [
          'type' => 'fraction',
          'label' => 'Total Award Budget',
          'description' => 'Total Award Budget',
          'required' => TRUE,
          'multiple' => FALSE,
      ],
      'csc_project_comet_version' => [
        'type' => 'string',
        'label' => 'COMET-Planner Version',
        'description' => 'COMET-Planner Version',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_project_year_reporting' => [
        'type' => 'list_string',
        'label' => 'Choose Year of Reporting',
        'description' => 'Choose Year of Reporting',
        'allowed_values' => [
          '2023' => t(string: '2023'),
        ],
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'csc_project_month_reporting' => [
        'type' => 'list_string',
        'label' => 'Choose Month of Reporting',
        'description' => 'Choose Month of Reporting',
        'allowed_values' => [
          'Jan 1 - March 31' => t(string: 'Jan 1 - March 31'),
          'April 1 - June 30' => t(string: 'April 1 - June 30'),
          'July 1 - September 30' => t(string: 'July 1 - September 30'),
          'October 1 - December 31' => t(string: 'October 1 - December 31'),
        ],
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      // 'file_upload' => [
      //     'type' => 'file',
      //     'label' => 'Excel file to be imported',
      //     // 'upload_validators' => [
      //     //   'file_validate_extensions' => [
      //     //     'png gif jpg',
      //     //   ],
      //     // ],
      // ],
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
