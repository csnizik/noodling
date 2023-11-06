<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Additional Environmental Benefits log type.
 *
 * @LogType(
 * id = "csc_environmental_benefits",
 * label = @Translation("EnvironmentalBenefits"),
 * )
 */
class EnvironmentalBenefits extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
      'csc_fiscal_year' => [
        'type' => 'string',
        'label' => 'Environmental Benefits Federal Fiscal Year of report submission',
        'description' => 'Environmental Benefits Federal Fiscal Year of report submission',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_fiscal_quarter' => [
        'type' => 'string',
        'label' => 'Environmental Benefits Federal Fiscal Quarter of report submission',
        'description' => 'Environmental Benefits Federal Fiscal Quarter of report submission',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_field_id' => [
        'type' => 'entity_reference',
        'label' => 'Environmental Benefits Field ID',
        'description' => 'Environmental Benefits Field ID',
		    'target_type' => 'asset',
		    'target_bundle' => 'csc_field_enrollment',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_project_id' => [
        'type' => 'entity_reference',
        'label' => 'Project ID',
        'description' => 'Project ID',
        'target_type' => 'asset',
        'target_bundle' => 'csc_project_summary',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_environmental_benefits' => [
        'type' => 'list_string',
        'label' => 'Environmental benefits',
        'description' => 'Environmental benefits',
		    'allowed_values' => [
          'Yes' => t(string: 'Yes'),
          'No' => t(string: 'No'),
          "I don't know" => t(string: "I don't Know"),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_nitrogen_loss' => [
        'type' => 'list_string',
        'label' => 'Reduction in Nitrogen Loss',
        'description' => 'Reduction in Nitrogen Loss',
        'allowed_values' => [
          'Yes' => t(string: 'Yes'),
          'No' => t(string: 'No'),
          "I don't know" => t(string: "I don't Know"),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_nitrogen_loss_amount' => [
        'type' => 'string',
        'label' => 'Reduction in Nitrogen Loss Amount',
        'description' => 'Reduction in Nitrogen Loss Amount',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_nitrogen_loss_amount_unit' => [
        'type' => 'list_string',
        'label' => 'Reduction in Nitrogen Loss Amount Unit',
        'description' => 'Reduction in Nitrogen Loss Amount Unit',
        'allowed_values' => [
          'Kilograms' => t(string: 'Kilograms'),
          'Metric Tons' => t(string: 'Metric Tons'),
          'Pounds' => t(string: 'Pounds'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_nitrogen_loss_amt_ut_otr' => [
        'type' => 'string',
        'label' => 'Other Reduction in Nitrogen Loss Amount Unit',
        'description' => 'Other Reduction in Nitrogen Loss Amount Unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_nitrogen_loss_purpose' => [
        'type' => 'list_string',
        'label' => 'Reduction in Nitrogen Loss Purpose',
        'description' => 'Reduction in Nitrogen Loss Purpose',
        'allowed_values' => [
          'Commodity Marketing' => t(string: 'Commodity Marketing'),
          'Producing Insets' => t(string: 'Producing Insets'),
          'Producing Offsets' => t(string: 'Producing Offsets'),
          "I don't know" => t(string: "I don't know"),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_nitrogen_loss_purpose_other' => [
        'type' => 'string',
        'label' => 'Other Reduction in Nitrogen Loss Purpose',
        'description' => 'Other Reduction in Nitrogen Loss Purpose',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_phosphorus_loss' => [
        'type' => 'list_string',
        'label' => 'Reduction in Phosphorus Loss',
        'description' => 'Reduction in Phosphorus Loss',
        'allowed_values' => [
          'Yes' => t(string: 'Yes'),
          'No' => t(string: 'No'),
          "I don't know" => t(string: "I don't Know"),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_phosphorus_loss_amount' => [
        'type' => 'string',
        'label' => 'Reduction in Phosphorus Loss Amount',
        'description' => 'Reduction in Phosphorus Loss Amount',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_phosphorus_loss_amount_unit' => [
        'type' => 'list_string',
        'label' => 'Reduction in Phosphorus Loss Amount Unit',
        'description' => 'Reduction in Phosphorus Loss Amount Unit',
        'allowed_values' => [
          'Kilograms' => t(string: 'Kilograms'),
          'Metric Tons' => t(string: 'Metric Tons'),
          'Pounds' => t(string: 'Pounds'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_phosphorus_loss_amt_ut_otr' => [
        'type' => 'string',
        'label' => 'Other Reduction in Phosphorus Loss Amount Unit',
        'description' => 'Other Reduction in Phosphorus Loss Amount Unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_phosphorus_loss_purpose' => [
        'type' => 'list_string',
        'label' => 'Reduction in Phosphorus Loss Purpose',
        'description' => 'Reduction in Phosphorus Loss Purpose',
        'allowed_values' => [
          'Commodity Marketing' => t(string: 'Commodity Marketing'),
          'Producing Insets' => t(string: 'Producing Insets'),
          'Producing Offsets' => t(string: 'Producing Offsets'),
          "I don't know" => t(string: "I don't know"),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_phosphorus_loss_purpose_otr' => [
        'type' => 'string',
        'label' => 'Other Reduction in Phosphorus Loss Purpose',
        'description' => 'Other Reduction in Phosphorus Loss Purpose',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_other_water_quality' => [
        'type' => 'list_string',
        'label' => 'Other Water Quality',
        'description' => 'Other Water Quality',
        'allowed_values' => [
          'Yes' => t(string: 'Yes'),
          'No' => t(string: 'No'),
          "I don't know" => t(string: "I don't Know"),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_other_water_quality_type' => [
        'type' => 'list_string',
        'label' => 'Other Water Quality Type',
        'description' => 'Other Water Quality Type',
        'allowed_values' => [
          'Sediment load reduction' => t(string: 'Sediment load reduction'),
          'Temperature' => t(string: 'Temperature'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_otr_water_quality_type_otr' => [
        'type' => 'string',
        'label' => 'Other - Other Water Quality Type',
        'description' => 'Other - Other Water Quality Type',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_other_water_quality_amount' => [
        'type' => 'fraction',
        'label' => 'Other Water Quality Amount',
        'description' => 'Other Water Quality Amount',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_otr_water_quality_amt_ut' => [
        'type' => 'list_string',
        'label' => 'Other Water Quality Amount Unit',
        'description' => 'Other Water Quality Amount Unit',
        'allowed_values' => [
          'Degrees F' => t(string: 'Degrees F'),
          'Kilograms' => t(string: 'Kilograms'),
          'Kilograms per litre' => t(string: 'Kilograms per litre'),
          'Metric Tons' => t(string: 'Metric Tons'),
          'Pounds' => t(string: 'Pounds'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_otr_h2o_quality_amt_ut_otr' => [
        'type' => 'string',
        'label' => 'Other - Other Water Quality Amount Unit',
        'description' => 'Other Other Water Quality Amount Unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_other_water_quality_purpose' => [
        'type' => 'list_string',
        'label' => 'Other Water Quality Purpose',
        'description' => 'Other Water Quality Purpose',
        'allowed_values' => [
          'Commodity Marketing' => t(string: 'Commodity Marketing'),
          'Producing Insets' => t(string: 'Producing Insets'),
          'Producing Offsets' => t(string: 'Producing Offsets'),
          "I don't know" => t(string: "I don't know"),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_otr_h2o_quality_purpose_otr' => [
        'type' => 'string',
        'label' => 'Other - other Water Quality Purpose',
        'description' => 'Other - Other Water Quality Purpose',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_water_quantity' => [
        'type' => 'list_string',
        'label' => 'Water Quantity',
        'description' => 'Water Quality',
        'allowed_values' => [
          'Yes' => t(string: 'Yes'),
          'No' => t(string: 'No'),
          "I don't know" => t(string: "I don't Know"),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_water_quantity_amount' => [
        'type' => 'fraction',
        'label' => 'Water Quantity Amount',
        'description' => 'Water Quantity Amount',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_water_quantity_amount_unit' => [
        'type' => 'list_string',
        'label' => 'Water Quantity Amount Unit',
        'description' => 'Water Quantity Amount Unit',
        'allowed_values' => [
          'Acre-Feet' => t(string: 'Acre-Feet'),
          'Cubic Feet' => t(string: 'Cubic Feet'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_water_quantity_amt_ut_otr' => [
        'type' => 'string',
        'label' => 'Other Water Quantity Amount Unit',
        'description' => 'Other Water Quantity Amount Unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_water_quantity_purpose' => [
        'type' => 'list_string',
        'label' => 'Water Quantity Purpose',
        'description' => 'Water Quantity Purpose',
        'allowed_values' => [
          'Commodity Marketing' => t(string: 'Commodity Marketing'),
          'Producing Insets' => t(string: 'Producing Insets'),
          'Producing Offsets' => t(string: 'Producing Offsets'),
          "I don't know" => t(string: "I don't know"),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_water_quantity_purpose_otr' => [
        'type' => 'string',
        'label' => 'Other Water Quantity Purpose',
        'description' => 'Other Water Quantity Purpose',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_reduced_erosion' => [
        'type' => 'list_string',
        'label' => 'Reduced Erosion',
        'description' => 'Reduced Erosion',
        'allowed_values' => [
          'Yes' => t(string: 'Yes'),
          'No' => t(string: 'No'),
          "I don't know" => t(string: "I don't Know"),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_reduced_erosion_amount' => [
        'type' => 'fraction',
        'label' => 'Reduced Erosion Amount',
        'description' => 'Reduced Erosion Amount',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_reduced_erosion_amount_unit' => [
        'type' => 'list_string',
        'label' => 'Reduced Erosion Amount Unit',
        'description' => 'Reduced Erosion Amount Unit',
        'allowed_values' => [
          'Tons' => t(string: 'Tons'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_reduced_erosion_amt_ut_otr' => [
        'type' => 'string',
        'label' => 'Other Reduced Erosion Amount Unit',
        'description' => 'Other Reduced Erosion Amount Unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_reduced_erosion_purpose' => [
        'type' => 'list_string',
        'label' => 'Reduced Erosion Purpose',
        'description' => 'Reduced Erosion Purpose',
        'allowed_values' => [
          'Commodity Marketing' => t(string: 'Commodity Marketing'),
          'Producing Insets' => t(string: 'Producing Insets'),
          'Producing Offsets' => t(string: 'Producing Offsets'),
          "I don't know" => t(string: "I don't know"),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_reduced_erosion_purpose_otr' => [
        'type' => 'string',
        'label' => 'Other Reduced Erosion Purpose',
        'description' => 'Other Reduced Erosion Purpose',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_reduced_energy_use' => [
        'type' => 'fraction',
        'label' => 'Reduced Energy Use',
        'description' => 'Reduced Energy Use',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_reduced_energy_use_amount' => [
        'type' => 'fraction',
        'label' => 'Reduced Energy Use Amount',
        'description' => 'Reduced Energy Use Amount',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_reduced_energy_use_amt_ut' => [
        'type' => 'list_string',
        'label' => 'Reduced Energy Use Amount Unit',
        'description' => 'Reduced Energy Use Amount Unit',
        'allowed_values' => [
          'Kilowatt Hours' => t(string: 'Kilowatt Hours'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_red_energy_use_amt_ut_otr' => [
        'type' => 'string',
        'label' => 'Other Reduced Energy Use Amount Unit',
        'description' => 'Other Reduced Energy Use Amount Unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_reduced_energy_use_purpose' => [
        'type' => 'list_string',
        'label' => 'Reduced Energy Use Purpose',
        'description' => 'reduced Energy Use Purpose',
        'allowed_values' => [
          'Commodity Marketing' => t(string: 'Commodity Marketing'),
          'Producing Insets' => t(string: 'Producing Insets'),
          'Producing Offsets' => t(string: 'Producing Offsets'),
          "I don't know" => t(string: "I don't know"),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_red_energy_use_purpose_otr' => [
        'type' => 'string',
        'label' => 'Other Reduced Energy Use Purpose',
        'description' => 'Other Reduced Energy Use Purpose',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_avoided_land_conversion' => [
        'type' => 'list_string',
        'label' => 'Avoided Land Conversion',
        'description' => 'Avoided Land Conversion',
        'allowed_values' => [
          'Yes' => t(string: 'Yes'),
          'No' => t(string: 'No'),
          "I don't know" => t(string: "I don't Know"),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_avoided_land_conversion_amt' => [
        'type' => 'fraction',
        'label' => 'Avoided Land Conversion Amount',
        'description' => 'Avoided Land Conversion Amount',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_avoided_land_conversion_ut' => [
        'type' => 'list_string',
        'label' => 'Avoided Land Conversion Unit',
        'description' => 'Avoided Land Conversion Unit',
        'allowed_values' => [
          'Acres' => t(string: 'Acres'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_avo_land_conversion_ut_otr' => [
        'type' => 'string',
        'label' => 'Other Avoided Land Conversion Unit',
        'description' => 'Other Avoided Land Conversion Unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_avo_land_conversion_purpose' => [
        'type' => 'list_string',
        'label' => 'Avoided Land Conversion Purpose',
        'description' => 'Avoided Land Conversion Purpose',
        'allowed_values' => [
          'Commodity Marketing' => t(string: 'Commodity Marketing'),
          'Producing Insets' => t(string: 'Producing Insets'),
          'Producing Offsets' => t(string: 'Producing Offsets'),
          "I don't know" => t(string: "I don't know"),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_avo_land_conv_purpose_otr' => [
        'type' => 'string',
        'label' => 'Other Avoided Land Conversion Purpose',
        'description' => 'Other Avoided Land Conversion Purpose',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_improved_wildlife_habitat' => [
        'type' => 'list_string',
        'label' => 'Improved Wildlife Habitat',
        'description' => 'Improved Wildlife Habitat',
        'allowed_values' => [
          'Yes' => t(string: 'Yes'),
          'No' => t(string: 'No'),
          "I don't know" => t(string: "I don't Know"),
        ],        
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_imp_wildlife_habitat_amt' => [
        'type' => 'fraction',
        'label' => 'Improved Wildlife Habitat Amount',
        'description' => 'Improved Wildlife Habitat Amount',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_imp_wildlife_habitat_ut' => [
        'type' => 'list_string',
        'label' => 'Improved Wildlife Habitat Unit',
        'description' => 'Improved Wildlife Habitat Unit',
        'allowed_values' => [
          'Acres' => t(string: 'Acres'),
          'Linear Feet' => t(string: 'Linear Feet'),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
      'csc_imp_wld_habitat_amt_ut_otr' => [
        'type' => 'string',
        'label' => 'Other Improved Wildlife Habitat Unit',
        'description' => 'Other Improved Wildlife Habitat Unit',
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_imp_wld_habitat_purpose' => [
        'type' => 'list_string',
        'label' => 'Improved Wildlife Habitat Purpose',
        'description' => 'improved Wildlife Habitat Purpose',
        'allowed_values' => [
          'Commodity Marketing' => t(string: 'Commodity Marketing'),
          'Producing Insets' => t(string: 'Producing Insets'),
          'Producing Offsets' => t(string: 'Producing Offsets'),
          "I don't know" => t(string: "I don't know"),
          'Other(specify)' => t(string: 'Other(specify)'),
        ],
        'required' => TRUE,
        'multiple' => FALSE,
      ],
	    'csc_imp_wld_habitat_purpose_otr' => [
        'type' => 'string',
        'label' => 'Other Improved Wildlife Habitat Purpose',
        'description' => 'Other Improved Wildlife Habitat Purpose',
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