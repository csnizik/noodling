<?php

namespace Drupal\cig_pods_csc\Plugin\Asset\AssetType;

use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the CIG Project asset type.
 *
 * @AssetType(
 * id = "project",
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
      'project_id' => [
        'type' => 'string',
        'label' => 'Project ID',
        'description' => 'Project ID',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'project_grantee_org' => [
        'type' => 'string',
        'label' => 'Grantee Organization Name',
        'description' => 'Grantee Organization Name',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'project_grantee_contact_name' => [
        'type' => 'string',
        'label' => 'Grantee Primary Point of Contact',
        'description' => 'Grantee Primary Point of Contact',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'project_grantee_contact_email' => [
        'type' => 'string',
        'label' => 'Grantee Primary Point of Contact Email',
        'description' => 'Grantee Primary Point of Contact Email',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'project_start' => [
        'type' => 'timestamp',
        'label' => 'Overall Project Start Date',
        'description' => 'Overall Project Start Date',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'project_end' => [
        'type' => 'timestamp',
        'label' => 'Overall Project End Date',
        'description' => 'Overall Project End Date',
        'required' => TRUE ,
        'multiple' => FALSE,
      ],
      'project_budget' => [
          'type' => 'fraction',
          'label' => 'Total Award Budget',
          'description' => 'Total Award Budget',
          'required' => TRUE,
          'multiple' => FALSE,
      ],
      'project_comet_version' => [
        'type' => 'string',
        'label' => 'COMET-Planner Version',
        'description' => 'COMET-Planner Version',
        'required' => TRUE ,
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
