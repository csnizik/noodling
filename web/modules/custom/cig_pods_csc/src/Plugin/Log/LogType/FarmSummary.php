<?php

namespace Drupal\cig_pods_csc\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;
use Drupal\farm_field\FarmFieldFactory;

/**
 * Provides the Farm Summary log type.
 *
 * @LogType(
 * id = "csc_farm_summary",
 * label = @Translation("FarmSummary"),
 * )
 */
class FarmSummary extends FarmLogType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {

    $fields = parent::buildFieldDefinitions();

    $field_info = [
        'csc_farm_summary_fiscal_year' => [
            'type' => 'string',
            'label' => 'Federal Fiscal Year of report submission',
            'description' => 'Federal Fiscal Year of report submission',
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_farm_summary_fiscal_quarter' => [
            'type' => 'string',
            'label' => 'Federal Fiscal Quarter of report submission',
            'description' => 'Federal Fiscal Quarter of report submission',
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          /*'csc_farm_summary_farm_id' => [
            'type' => '',
            'label' => 'Farm ID',
            'description' => '',
            'required' => TRUE,
            'multiple' => FALSE,
          ],*/
          'csc_farm_summary_state' => [
            'type' => 'entity_reference',
            'label' => 'State or territory',
            'description' => 'State or territory',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'state',
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_farm_summary_county' => [
            'type' => 'entity_reference',
            'label' => 'County',
            'description' => 'County',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'county',
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_fa_summ_prdcr_ta_rcvd' => [
            'type' => 'entity_reference',
            'label' => 'Producer TA received',
            'description' => 'Did the lead grantee or any partner provide technical assistance (TA) to the producer this year? Technical assistance is any training, education, capacity building or other support provided by any project partner(s) directly to producers enrolled in the project. List up to the top three most common types of TA provided to this producer. The worksheet provides three columns with a drop‐down list of the allowed values. Choose one value for each column. If there are fewer than 3 TA types, leave unnecessary columns blank. If “other” is chosen, use the additional column to enter other TA types as free text.',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'producer_ta_received',
            'required' => TRUE,
            'multiple' => TRUE,
          ],
          'csc_fa_summ_prdcr_ta_rcvd_otr' => [
            'type' => 'string',
            'label' => 'Other producer TA received',
            'description' => 'Other producer TA received',
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_fa_summ_prdcr_inc_amt' => [
            'type' => 'fraction',
            'label' => 'Producer incentive amount',
            'description' => 'Total incentive payment received by the producer from USDA project funds for the year (non‐ cumulative). Do not include incentive payments made with partner match funds.',
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_fa_summ_inc_reason' => [
            'type' => 'entity_reference',
            'label' => 'Incentive reason',
            'description' => 'List up to four reasons for producer incentive payments. List the top 4 based on total value of the incentive for each reason. The worksheet provides four columns with a drop‐down list of the allowed values. Choose one value for each column. If there are fewer than 4 reasons, leave unnecessary columns blank. If “other” is chosen, use the additional column to enter other reasons as free text.',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'incentive_reason',
            'required' => TRUE,
            'multiple' => TRUE,
          ],
          'csc_fa_summ_inc_reason_otr' => [
            'type' => 'string',
            'label' => 'Other incentive reason',
            'description' => 'Other incentive reason',
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_fa_summ_inc_structure' => [
            'type' => 'entity_reference',
            'label' => 'Incentive structure',
            'description' => 'List the structures (units) corresponding to the top 4 (by dollar value) incentive payments to producers. Production unit is weight or volume (bushel, kilogram, ton). The worksheet provides four columns with a drop‐down list of the allowed values. Choose one value for each column. If there are fewer than 4 structure types, leave unnecessary columns blank. If “other” is chosen, use the additional column to enter other structure types as free text',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'incentive_structure',
            'required' => TRUE,
            'multiple' => TRUE,
          ],
          'csc_fa_summ_inc_structure_otr' => [
            'type' => 'string',
            'label' => 'Other incentive structure',
            'description' => 'Other incentive structure',
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_farm_summary_incentive_type' => [
            'type' => 'entity_reference',
            'label' => 'Incentive type',
            'description' => 'List the top 4 types of incentive payments to producers (based on dollar value). The worksheet provides four columns with a drop‐down list of the allowed values. Choose one value for each column. If there are fewer than 4 incentive types, leave unnecessary columns blank. If “other” is chosen, use the additional column to enter other incentive types as free text.',
            'target_type' => 'taxonomy_term',
            'target_bundle' => 'incentive_type',
            'required' => TRUE,
            'multiple' => TRUE,
          ],
          'csc_fa_summ_inc_type_otr' => [
            'type' => 'string',
            'label' => 'Other incentive type',
            'description' => 'Other incentive type',
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_fa_summ_pay_on_enrollment' => [
            'type' => 'list_string',
            'label' => 'Payment on enrollment',
            'description' => 'Any incentive payment provided to the producer upon enrollment/signing a contract, and not related to any implementation, MMRV or sales activities. Full payment means the full incentive amount for any contract held by the producer is paid upon enrollment. Partial payment means that only part of the full incentive amount for any contract held by the producer is paid upon enrollment. No payment means that none of the full incentive amount for any contract held by the producer is paid upon enrollment.',
            'allowed_values' => [
                'Full payment' => t(string: 'Full payment'),
                'Partial payment' => t(string: 'Partial payment'),
                'No payment' => t(string: 'No payment'),
            ],
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_fa_summ_pay_on_impl' => [
            'type' => 'list_string',
            'label' => 'Payment on implementation',
            'description' => ': Any incentive payment provided to the producer upon implementing the practices included in the contract. Full payment means the full incentive amount for any contract held by the producer is paid upon implementation. Partial payment means that only part of the full incentive amount for any contract held by the producer is paid upon implementation. No payment means that none of the full incentive amount for any contract held by the producer is paid upon implementation.',
            'allowed_values' => [
                'Full payment' => t(string: 'Full payment'),
                'Partial payment' => t(string: 'Partial payment'),
                'No payment' => t(string: 'No payment'),
            ],
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_fa_summ_pay_on_harvest' => [
            'type' => 'list_string',
            'label' => 'Payment on harvest',
            'description' => 'Any incentive payment provided to the producer upon harvesting or slaughtering the commodity included in the contract. Full payment means the full incentive amount for any contract held by the producer is paid upon harvest. Partial payment means that only part of the full incentive amount for any contract held by the producer is paid upon harvest. No payment means that none of the full incentive amount for any contract held by the producer is paid upon harvest.',
            'allowed_values' => [
                'Full payment' => t(string: 'Full payment'),
                'Partial payment' => t(string: 'Partial payment'),
                'No payment' => t(string: 'No payment'),
            ],
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_fa_summ_pay_on_mmrv' => [
            'type' => 'list_string',
            'label' => 'Payment on MMRV',
            'description' => 'Any incentive payment provided to the producer upon completing the annual MMRV requirements included in the contract. Full payment means the full incentive amount for any contract held by the producer is paid upon MMRV being complete. Partial payment means that only part of the full incentive amount for any contract held by the producer is paid upon MMRV being complete. No payment means that none of the full incentive amount for any contract held by the producer is paid upon MMRV being complete.',
            'allowed_values' => [
                'Full payment' => t(string: 'Full payment'),
                'Partial payment' => t(string: 'Partial payment'),
                'No payment' => t(string: 'No payment'),
            ],
            'required' => TRUE,
            'multiple' => FALSE,
          ],
          'csc_fa_summ_pay_on_sale' => [
            'type' => 'list_string',
            'label' => 'Payment on sale',
            'description' => 'Any incentive payment provided to the producer upon sale of the commodity included in the contract. Full payment means the full incentive amount for any contract held by the producer is paid upon sale. Partial payment means that only part of the full incentive amount for any contract held by the producer is paid upon sale. No payment means that none of the full incentive amount for any contract held by the producer is paid upon sale.',
            'allowed_values' => [
                'Full payment' => t(string: 'Full payment'),
                'Partial payment' => t(string: 'Partial payment'),
                'No payment' => t(string: 'No payment'),
            ],
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