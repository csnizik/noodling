<?php

namespace Drupal\cig_pods_csc\Plugin\Asset\AssetType;

use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the CIG csc_project asset type.
 *
 * @AssetType(
 * id = "csc_partner_activities",
 * label = @Translation("Awardee"),
 * )
 */
class PartnerActivities extends FarmAssetType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
        'csc_prtnr_act_partner_ein' => [
            'type' => 'string',
            'label' => 'Partner ID (EIN)',
            'description' => '',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_partner_type' => [
            'type' => 'entity_reference',
            'label' => 'Partner Type',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'partner_type',
            'description' => 'Legal/financial structure of lead grantee of partner organization.',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_partner_poc' => [
            'type' => 'string',
            'label' => 'Partner POC',
            'description' => 'Name of a point of contact for the lead grantee or partner organization.',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_partner_poc_email' => [
            'type' => 'email',
            'label' => 'Partner POC Email',
            'description' => 'Email of the point of contact for the lead grantee or partner organization.',
            '#pattern' => '*@example.com',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_partnership_start' => [
            'type' => 'timestamp',
            'label' => 'Partnership Start Date',
            'description' => 'Date (Month, Year) that the partner organization and the lead grantee began formally partnering.',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_partnership_end' => [
            'type' => 'timestamp',
            'label' => 'Partnership End Date',
            'description' => 'Date (Month, Year) that the partner organization and the lead grantee stopped formally partnering on the project.',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_partnership_initation' => [
            'type' => 'boolean',
            'label' => 'New Partnership',
            'description' => 'A new partnership means that the lead grantee and the partner organization have not had a formal working relationship (under contract or on a grant) prior to the start of the project.',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_partner_total_requested' => [
            'type' => 'fraction',
            'label' => 'Partner Total Requested',
            'description' => 'Cumulative (total) amount of funds that the partner has requested reimbursement for from the lead grantee from the start of the partnership to the end of the reporting quarter. For each quarter’s data entry, the value must be the sum of all previous entries plus the amount of funds requested in the reporting quarter. If there are no changes, report the value from the previous quarter',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_total_match_contrib' => [
            'type' => 'fraction',
            'label' => 'Total Match Contribution',
            'description' => 'Cumulative (total) value of funds and in‐kind contributions (e.g., staff time, inputs, equipment rental, marketing support) that the partner has provided as a project match contribution from the start of the partnership to the end of the reporting quarter. For each quarter’s data entry, the value must be the sum of all previous entries plus match contributions in the reporting quarter. If there are no changes, report the value from the previous quarter.',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_total_match_incent' => [
            'type' => 'fraction',
            'label' => 'Total Match Incentives',
            'description' => 'Cumulative (total) value of funds for incentive payments directly to producers that the partner has provided as a project match contribution from the start of the partnership to the end of the reporting quarter. For each quarter’s data entry, the value must be the sum of all previous entries plus match incentives in the reporting quarter. If there are no changes, report the value from the previous quarter.',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_match_type_1' => [
            'type' => 'entity_reference',
            'label' => 'Match Type 1',
            'description' => 'Types of match contributions other than incentives provided directly to producers by the organization from the start of the partnership to the end of the reporting quarter. Enter up to the top three (in dollar value) types of match contributions provided. In‐kind staff time could be used for technical assistance, marketing assistance, or other support to producers. Production inputs include seed, fertilizer, pesticides, equipment and other inputs for use in the field. The worksheet provides three columns with a drop‐down list of the allowed values. Choose one value for each column. If fewer than 3 match types are used, leave unnecessary columns blank. If “other” is chosen, use the additional column to enter other match types as free text.',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'match_type',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_match_amount_1' => [
            'type' => 'fraction',
            'label' => 'Match Amount 1',
            'description' => 'Cumulative (total) value of funds for each match type that the organization has provided as a project match contribution from the start of the partnership to the end of the reporting quarter. Enter amounts for up to the top three (in dollar value) match types. The worksheet provides three columns for this data element. Enter one value for each column. If fewer than 3 match types are used, leave unnecessary columns blank.',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_match_type_2' => [
            'type' => 'entity_reference',
            'label' => 'Match Type 2',
            'description' => '',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'match_type',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_match_amount_2' => [
            'type' => 'fraction',
            'label' => 'Match Amount 2',
            'description' => '',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_match_type_3' => [
            'type' => 'entity_reference',
            'label' => 'Match Type 3',
            'description' => '',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'match_type',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_match_amount_3' => [
            'type' => 'fraction',
            'label' => 'Match Amount 3',
            'description' => '',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_match_type_other' => [
            'type' => 'string',
            'label' => 'Other Match Type',
            'description' => '',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_training_provided' => [
            'type' => 'entity_reference',
            'label' => 'Training Type Provided',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'training_provided',
            'description' => 'Types of training provided to the project partner as a result of participating in the project during the past quarter. Training can come from the lead grantee, a project partner organization (including other divisions of their own organization, or an outside organization. Enter up to the top three (in dollar value) types of partner training provided. The worksheet provides three columns with a drop‐down list of the allowed values. Choose one value for each column. If fewer than 3 training types are used, leave unnecessary columns blank. If “other” is chosen, use the additional column to enter other training types as free text.',
            'required' => TRUE,
            'multiple' => TRUE,
        ],
        'csc_prtnr_act_training_other' => [
            'type' => 'string',
            'label' => 'Other Training Type',
            'description' => '',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_partner_activity_activity1' => [
            'type' => 'entity_reference',
            'label' => 'Activity 1',
            'description' => 'Types of activities that the lead grantee or partner organization has provided during the reporting quarter. Enter up to the top three (in dollar value) types of activities undertaken. The worksheet provides three columns with a drop‐down list of the allowed values. Choose one value for each column. If fewer than 3 activity types are used, leave unnecessary columns blank. If “other” is chosen, use the additional column to enter other activity types as free text.',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'activity_by_partner',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_activity1_cost' => [
            'type' => 'fraction',
            'label' => 'Activity 1 Cost',
            'description' => 'Cumulative (total) cost of each activity type that the organization has undertaken or offered from the start of the partnership to the end of the reporting quarter. Enter amounts for up to the top three (in dollar value) activity types. The worksheet provides three columns for this data element. Enter one value for each column. If fewer than 3 activity types are provided, leave unnecessary columns blank.',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_partner_activity_activity2' => [
            'type' => 'entity_reference',
            'label' => 'Activity 2',
            'description' => '',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'activity_by_partner',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_activity2_cost' => [
            'type' => 'fraction',
            'label' => 'Activity 2 Cost',
            'description' => '',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_partner_activity_activity3' => [
            'type' => 'entity_reference',
            'label' => 'Activity 3',
            'description' => '',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'activity_by_partner',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_activity3_cost' => [
            'type' => 'fraction',
            'label' => 'Activity 3 Cost',
            'description' => '',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_activity_other' => [
            'type' => 'string',
            'label' => 'Other Activity By Partner',
            'description' => '',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_products_supplied' => [
            'type' => 'string',
            'label' => 'Products Supplied',
            'description' => 'Name(s) of products supplied to enrolled producers as incentives or matching contributions. Enter the name of each product, including its brand. Separate each product name with a comma. If no products or supplies were provided by the organization, leave the column blank',
            'required' => TRUE,
            'multiple' => FALSE,
        ],
        'csc_prtnr_act_product_source' => [
            'type' => 'string',
            'label' => 'Products Source',
            'description' => 'Name of firm or company from which supplies were obtained.',
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