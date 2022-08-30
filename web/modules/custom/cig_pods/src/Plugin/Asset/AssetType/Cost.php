<?php

namespace Drupal\cig_pods\Plugin\Asset\AssetType;

use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;
use Drupal\farm_field\FarmFieldFactory;

/**
   * Provides the Cost asset type.
   *
   * @AssetType(
   * id = "cost",
   * label = @Translation("Cost"),
   * description = @Translation("Cost")
   * )
   */
class Cost extends FarmAssetType {

   public function buildFieldDefinitions() {
      $fields = parent::buildFieldDefinitions();

      $field_info = [
         'field_cost_amount' => [
            'label'=> 'Cost',
            'type'=> 'fraction',
            'required' => FALSE,
            'description' => '',
         ],
         'field_cost_type' => [
            'label'=> 'Type',
            'type'=> 'entity_reference',
            'target_type'=> 'taxonomy_term',
            'target_bundle'=> 'd_cost_type',
            'required' => FALSE,
            'description' => '',
         ],
        'project' =>[
          'label' => 'Project',
          'type' => 'entity_reference',
          'target_type' => 'asset',
          'target_bundle' => 'project',
          'required' => TRUE,
          'multiple' => TRUE,
        ],
      ];

      $farmFieldFactory = new FarmFieldFactory();
      foreach($field_info as $name => $info){

		$fields[$name] = $farmFieldFactory->bundleFieldDefinition($info)
					      -> setDisplayConfigurable('form',TRUE)
					      -> setDisplayConfigurable('view', TRUE);
      }

      return $fields;

   }

}